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

use Intacct\Adv2016\DataDeliveryService\Exceptions\CsvFileException;
use League\Csv\Reader;
use Psr\Http\Message\StreamInterface;

class CsvFile
{

    /** @var Reader */
    private $csvReader;

    /** @var string */
    private $objectName;

    /** @var JobType */
    private $jobType;

    /** @var int */
    private $recordNo;

    /** @var string */
    private $timestamp;

    /**
     * @return Reader
     */
    public function getCsvReader()
    {
        return $this->csvReader;
    }

    /**
     * @param Reader $csvReader
     */
    public function setCsvReader($csvReader)
    {
        $this->csvReader = $csvReader;
    }

    /**
     * @return string
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * @param string $objectName
     */
    public function setObjectName($objectName)
    {
        $this->objectName = $objectName;
    }

    /**
     * @return JobType
     */
    public function getJobType()
    {
        return $this->jobType;
    }

    /**
     * @param JobType $jobType
     */
    public function setJobType($jobType)
    {
        $this->jobType = $jobType;
    }

    /**
     * @return int
     */
    public function getRecordNo()
    {
        return $this->recordNo;
    }

    /**
     * @param int $recordNo
     */
    public function setRecordNo($recordNo)
    {
        $this->recordNo = $recordNo;
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * CsvFile constructor.
     *
     * @param string $filePath
     * @param StreamInterface $fileBody
     */
    public function __construct($filePath, StreamInterface $fileBody)
    {
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);
        $parts = explode('.', $fileName, 4);
        if (!is_array($parts) || count($parts) != 4) {
            throw new CsvFileException("File Path could not be parsed properly into 4 pieces, '{$filePath}'");
        }

        list($objectName, $jobType, $recordNo, $timestamp) = $parts;


        $this->setObjectName($objectName);
        $this->setJobType(new JobType($jobType));
        $this->setRecordNo($recordNo);
        $this->setTimestamp($timestamp);

        $this->setCsvReader(Reader::createFromString($fileBody));
    }
}
