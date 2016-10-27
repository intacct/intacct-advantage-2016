<?php

/**
 * Copyright 2016 Intacct Corporation.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"). You may not
 * use this file except in compliance with the License. You may obtain a copy
 * of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * or in the "LICENSE" file accompanying this file. This file is distributed on
 * an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Intacct\Adv2016\DataDeliveryService;

use Aws\Sdk as AwsSdk;
use Intacct\Adv2016\DataDeliveryService\Exceptions\LoaderException;
use Monolog\Logger;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Loader
{

    /** @var Container */
    private $ci;

    /** @var Logger */
    private $logger;

    /** @var \PDO */
    private $dbh;

    /**
     * Controller constructor.
     *
     * @param Container $ci
     */
    public function __construct(Container $ci) {
        $this->ci = $ci;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws \Exception
     */
    public function processCsvFromS3(Request $request, Response $response, array $args) {
        $messageId = $request->getHeader('X-Aws-Sqsd-Msgid');
        if (!$messageId) {
            $response->withStatus(400);
            return $response->write('SQS message ID header not provided');
        }

        /** @var Logger $logger */
        $logger = $this->ci->get('logger');
        $this->logger = $logger->withName($messageId);

        $this->dbh = $this->ci->get('db');

        $this->logger->addInfo('Processing new SQS message');

        // SQS posts JSON
        $message = $request->getParsedBody();
        $record = $message['Records'][0];
        $this->logger->addDebug('SQS record info', $record);

        $aws = new AwsSdk([
            'region' => $record['awsRegion'],
        ]);
        $s3 = $aws->createS3([
            'version' => '2006-03-01',
        ]);

        $s3BucketName = $record['s3']['bucket']['name'];
        $s3ObjectKey = $record['s3']['object']['key'];

        try {
            $s3Result = $s3->getObject([
                'Bucket' => $s3BucketName,
                'Key' => $s3ObjectKey,
            ]);

            $this->logger->addInfo('Loading CSV file');
            $csvFile = new CsvFile($s3ObjectKey, $s3Result['Body']);
            $this->logger->addDebug(
                'CSV file info',
                [
                    'Object name' => $csvFile->getObjectName(),
                    'Job type' => $csvFile->getJobType(),
                    'Record number' => $csvFile->getRecordNo(),
                    'Timestamp' => $csvFile->getTimestamp(),
                ]
            );

            $this->processCsvFileToDb($csvFile);

        } catch (\Exception $e) {
            $this->logger->addError(
                'DDS Loader exception caught',
                [
                    'Class' => get_class($e),
                    'Message' => $e->getMessage(),
                    'Trace' => $e->getTrace(),
                ]
            );

            // Send back a 200 http status code so SQS removes this message from the queue
            $response->withStatus(200);

            // Move the CSV file somewhere for review
            /*$s3->copyObject([
                'Bucket' => $s3BucketName,
                'Key' => '',
                'CopySource' => "{$s3BucketName}/{$s3ObjectKey}"
            ]);*/
        }

        return $response;
    }

    private function processCsvFileToDb(CsvFile $csvFile)
    {
        $this->logger->addInfo('Loading Object DML');
        $objectDml = new ObjectDml(
            $csvFile->getObjectName(),
            $this->ci->get('dml')
        );

        $csvReader = $csvFile->getCsvReader();

        // Flip the array so it's header_name => column_number
        $csvHeaders = array_flip($csvReader->fetchOne(0));
        $this->logger->addDebug(
            'CSV headers',
            $csvHeaders
        );

        try {
            $this->logger->addInfo('Beginning database transaction');
            $this->dbh->beginTransaction();

            foreach ($csvReader as $rowIndex => $csvRow) {
                if ($rowIndex > 0) {
                    $this->processCsvRowToDb(
                        $rowIndex,
                        $csvRow,
                        $csvFile->getJobType(),
                        $objectDml,
                        $csvHeaders
                    );
                }
            }

            // Commit the transaction to the db
            $this->logger->addInfo('Committing database transaction');
            $this->dbh->commit();

        } catch (\Exception $e) {
            // If there are any errors loading, roll back everything
            $this->dbh->rollBack();
            throw new LoaderException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @param int $rowIndex
     * @param array $csvRow
     * @param string $jobType
     * @param ObjectDml $objectDml
     * @param array $csvHeaders
     */
    private function processCsvRowToDb(
        $rowIndex,
        array $csvRow,
        $jobType,
        ObjectDml $objectDml,
        array $csvHeaders
    )
    {
        $rowNo = $rowIndex + 1;
        $this->logger->addInfo('Processing CSV row ' . $rowNo);
        $statement = null;
        $params = [];

        if ($jobType == JobType::ALL) {
            // Everything in the file is an insert
            $statement = $objectDml->getInsertStatement();
            $params = $objectDml->getInsertParams();
        } elseif ($jobType == JobType::CHANGE) {
            // SQL Statement will depend on the ddsChangeType column value in each row
            $changeType = new ChangeType($csvRow[$csvHeaders['ddsChangeType']]);

            if ($changeType == 'create') {
                $statement = $objectDml->getInsertStatement();
                $params = $objectDml->getInsertParams();
            } elseif ($changeType == 'update') {
                $statement = $objectDml->getUpdateStatement();
                $params = $objectDml->getUpdateParams();
            } elseif ($changeType == 'delete') {
                $statement = $objectDml->getDeleteStatement();
                $params = $objectDml->getDeleteParams();
            }
        }

        $this->logger->addInfo('Preparing database statement for CSV row ' . $rowNo);
        $sth = $this->dbh->prepare($statement);
        $this->logger->addInfo('Binding values to database statement for CSV row ' . $rowNo);
        foreach ($params as $key => $param) {
            if (!isset($csvHeaders[$param['header']])) {
                throw new LoaderException(
                    "The param '{$key}' header '{$param['header']}' cannot be found in the CSV headers."
                );
            }

            $columnIndex = $csvHeaders[$param['header']];
            $dataType = isset($param['type']) ? $param['type'] : \PDO::PARAM_STR;
            $value = $csvRow[$columnIndex];

            $sth->bindValue(":$key", !empty($value) ? $value : null, $dataType);
        }

        try {
            $this->logger->addInfo('Executing database statement for CSV row ' . $rowNo);
            $sth->execute();

        } catch (\Exception $e) {
            throw new LoaderException(
                "Database Load exception at row {$rowNo}. " . $e->getMessage(),
                $e->getCode(),
                $e->getPrevious()
            );
        }
    }
}
