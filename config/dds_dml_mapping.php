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

return [
    'CUSTOMER' => [
        'insert' => [
            'sql' => "INSERT INTO ia_customer (recordno, customerid, customername, onetime, status, displaycontactkey,"
                . " primarycontactkey, shiptocontactkey, billtocontactkey, whencreated, whenmodified)"
                . " VALUES (:recordno, :entity, :customername, :onetime, :status, :displaycontactkey, :primarycontactkey,"
                . " :shiptocontactkey, :billtocontactkey, to_timestamp(:whencreated, 'YYYY-MM-DD_HH24:MI:SS'),"
                . " to_timestamp(:whenmodified, 'YYYY-MM-DD_HH24:MI:SS'))",
            'params' => [
                'recordno' => [
                    'header' => 'RECORDNO',
                    'type' => \PDO::PARAM_INT,
                ],
                'entity' => [
                    'header' => 'ENTITY',
                ],
                'customername' => [
                    'header' => 'NAME',
                ],
                'onetime' => [
                    'header' => 'ONETIME',
                ],
                'status' => [
                    'header' => 'STATUS',
                ],
                'displaycontactkey' => [
                    'header' => 'DISPLAYCONTACTKEY',
                    'type' => \PDO::PARAM_INT,
                ],
                'primarycontactkey' => [
                    'header' => 'CONTACTKEY',
                    'type' => \PDO::PARAM_INT,
                ],
                'shiptocontactkey' => [
                    'header' => 'SHIPTOKEY',
                    'type' => \PDO::PARAM_INT,
                ],
                'billtocontactkey' => [
                    'header' => 'BILLTOKEY',
                    'type' => \PDO::PARAM_INT,
                ],
                'whencreated' => [
                    'header' => 'WHENCREATED',
                ],
                'whenmodified' => [
                    'header' => 'WHENMODIFIED',
                ],
            ],
        ],
        'update' => [
            'sql' => "UPDATE ia_customer SET customerid = :entity, customername = :customername, onetime = :onetime,"
                . " status = :status, displaycontactkey = :displaycontactkey, primarycontactkey = :primarycontactkey,"
                . " shiptocontactkey = :shiptocontactkey, billtocontactkey = :billtocontactkey,"
                . " whencreated = to_timestamp(:whencreated, 'YYYY-MM-DD_HH24:MI:SS'),"
                . " whenmodified = to_timestamp(:whenmodified, 'YYYY-MM-DD_HH24:MI:SS') WHERE recordno = :recordno",
            'params' => [
                'entity' => [
                    'header' => 'ENTITY',
                ],
                'customername' => [
                    'header' => 'NAME',
                ],
                'onetime' => [
                    'header' => 'ONETIME',
                ],
                'status' => [
                    'header' => 'STATUS',
                ],
                'displaycontactkey' => [
                    'header' => 'DISPLAYCONTACTKEY',
                    'type' => \PDO::PARAM_INT,
                ],
                'primarycontactkey' => [
                    'header' => 'CONTACTKEY',
                    'type' => \PDO::PARAM_INT,
                ],
                'shiptocontactkey' => [
                    'header' => 'SHIPTOKEY',
                    'type' => \PDO::PARAM_INT,
                ],
                'billtocontactkey' => [
                    'header' => 'BILLTOKEY',
                    'type' => \PDO::PARAM_INT,
                ],
                'whencreated' => [
                    'header' => 'WHENCREATED',
                ],
                'whenmodified' => [
                    'header' => 'WHENMODIFIED',
                ],
                'recordno' => [
                    'header' => 'RECORDNO',
                    'type' => \PDO::PARAM_INT,
                ],
            ],
        ],
        'delete' => [
            'sql' => "DELETE FROM ia_customer WHERE recordno = :recordno",
            'params' => [
                'recordno' => [
                    'header' => 'RECORDNO',
                    'type' => \PDO::PARAM_INT,
                ],
            ],
        ],
    ],
];