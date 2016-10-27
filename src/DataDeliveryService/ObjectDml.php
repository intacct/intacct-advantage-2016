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

use Intacct\Adv2016\DataDeliveryService\Exceptions\ObjectDmlException;

class ObjectDml
{

    /** @var string */
    private $insertStatement;

    /** @var array */
    private $insertParams = [];

    /** @var string|null */
    private $updateStatement = null;

    /** @var array */
    private $updateParams = [];

    /** @var string|null */
    private $deleteStatement = null;

    /** @var array */
    private $deleteParams = [];

    /**
     * @return string
     */
    public function getInsertStatement()
    {
        return $this->insertStatement;
    }

    /**
     * @param string $insertStatement
     */
    public function setInsertStatement($insertStatement)
    {
        $this->insertStatement = $insertStatement;
    }

    /**
     * @return array
     */
    public function getInsertParams()
    {
        return $this->insertParams;
    }

    /**
     * @param array $insertParams
     */
    public function setInsertParams($insertParams)
    {
        $this->insertParams = $insertParams;
    }

    /**
     * @return null|string
     */
    public function getUpdateStatement()
    {
        return $this->updateStatement;
    }

    /**
     * @param null|string $updateStatement
     */
    public function setUpdateStatement($updateStatement)
    {
        $this->updateStatement = $updateStatement;
    }

    /**
     * @return array
     */
    public function getUpdateParams()
    {
        return $this->updateParams;
    }

    /**
     * @param array $updateParams
     */
    public function setUpdateParams($updateParams)
    {
        $this->updateParams = $updateParams;
    }

    /**
     * @return null|string
     */
    public function getDeleteStatement()
    {
        return $this->deleteStatement;
    }

    /**
     * @param null|string $deleteStatement
     */
    public function setDeleteStatement($deleteStatement)
    {
        $this->deleteStatement = $deleteStatement;
    }

    /**
     * @return array
     */
    public function getDeleteParams()
    {
        return $this->deleteParams;
    }

    /**
     * @param array $deleteParams
     */
    public function setDeleteParams($deleteParams)
    {
        $this->deleteParams = $deleteParams;
    }

    /**
     * ObjectDml constructor.
     *
     * @param string $objectName
     * @param array $ddsDml
     */
    public function __construct($objectName, array $ddsDml)
    {
        if (!isset($ddsDml[$objectName])) {
            throw new ObjectDmlException(
                "The section [{$objectName}] could not be found in the DDS DML mapping"
            );
        }

        // Always require an insert statement and insert_params array
        if (!isset($ddsDml[$objectName]['insert']['sql'])) {
            throw new ObjectDmlException(
                "The section [{$objectName}] in the DDS DML mapping is missing an insert SQL statement"
            );
        }
        if (!isset($ddsDml[$objectName]['insert']['params'])) {
            throw new ObjectDmlException(
                "The section [{$objectName}] in the DDS DML mapping is missing an insert params array"
            );
        }
        $this->setInsertStatement($ddsDml[$objectName]['insert']['sql']);
        $this->setInsertParams($ddsDml[$objectName]['insert']['params']);

        // Update is optional
        if (isset($ddsDml[$objectName]['update']['sql'])) {
            // update_params array is required with an update statement
            if (!isset($ddsDml[$objectName]['update']['params'])) {
                throw new ObjectDmlException(
                    "The section [{$objectName}] in the DDS DML mapping is missing a update params array"
                );
            }
            $this->setUpdateStatement($ddsDml[$objectName]['update']['sql']);
            $this->setUpdateParams($ddsDml[$objectName]['update']['params']);
        }

        // Delete is optional
        if (isset($ddsDml[$objectName]['delete']['sql'])) {
            // delete_params array is required with an delete statement
            if (!isset($ddsDml[$objectName]['delete']['params'])) {
                throw new ObjectDmlException(
                    "The section [{$objectName}] in the DDS DML mapping is missing a delete params array"
                );
            }
            $this->setDeleteStatement($ddsDml[$objectName]['delete']['sql']);
            $this->setDeleteParams($ddsDml[$objectName]['delete']['params']);
        }
    }
}
