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

use Intacct\Adv2016\DataDeliveryService\Exceptions\ChangeTypeException;

class ChangeType
{

    /** @var string */
    const CREATE = 'create';

    /** @var string */
    const UPDATE = 'update';

    /** @var string */
    const DELETE = 'delete';

    /** @var string */
    private $changeType;

    /**
     * @return string
     */
    public function getChangeType()
    {
        return $this->changeType;
    }

    /**
     * @param string $changeType
     */
    public function setChangeType($changeType)
    {
        $changeTypes = [
            self::CREATE,
            self::UPDATE,
            self::DELETE,
        ];
        if (!in_array($changeType, $changeTypes)) {
            throw new ChangeTypeException("Invalid change type, '$changeType'");
        }
        $this->changeType = $changeType;
    }

    /**
     * ChangeType constructor.
     *
     * @param string $changeType
     */
    public function __construct($changeType)
    {
        $this->setChangeType($changeType);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->changeType;
    }
}
