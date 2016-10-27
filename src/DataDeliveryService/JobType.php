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

use Intacct\Adv2016\DataDeliveryService\Exceptions\JobTypeException;

class JobType
{

    /** @var string */
    const ALL = 'all';

    /** @var string */
    const CHANGE = 'change';

    /** @var string */
    private $jobType;

    /**
     * @return string
     */
    public function getJobType()
    {
        return $this->jobType;
    }

    /**
     * @param string $jobType
     */
    public function setJobType($jobType)
    {
        $jobTypes = [
            self::ALL,
            self::CHANGE,
        ];
        if (!in_array($jobType, $jobTypes)) {
            throw new JobTypeException("Invalid job type, '$jobType'");
        }
        $this->jobType = $jobType;
    }

    /**
     * JobType constructor
     *
     * @param string $jobType
     */
    public function __construct($jobType)
    {
        $this->setJobType($jobType);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->jobType;
    }
}
