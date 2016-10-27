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

// Include global autoloader
$loader = require __DIR__ . '/../vendor/autoload.php';

// Setup Slim App config
$config = [
    'name' => 'intacct-adv2016',
    'displayErrorDetails' => false, // Change me!
    'addContentLengthHeader' => false,
    'db' => [
        'host' => $_SERVER['RDS_HOSTNAME'],
        'port' => $_SERVER['RDS_PORT'],
        'dbname' => $_SERVER['RDS_DB_NAME'],
        'user' => $_SERVER['RDS_USERNAME'],
        'pass' => $_SERVER['RDS_PASSWORD'],
    ],
    'dds' => [
        'dml_mapping_file' => __DIR__ . '/dds_dml_mapping.php',
        // Configured in /.ebextensions/*logs.config
        'log_location' => '/var/log/dds-loader.log',
    ],
];

// Construct the Slim App
$app = new \Slim\App(['settings' => $config]);
$container = $app->getContainer();

// Setup the logging
$container['logger'] = function($c) {
    $settings = $c['settings'];
    $logger = new \Monolog\Logger($settings['name']);

    $dds = new \Monolog\Handler\StreamHandler(
        $settings['dds']['log_location'],
        \Monolog\Logger::DEBUG
    );
    $logger->pushHandler($dds);
    return $logger;
};

// Setup the database
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    // RDS for PostgreSQL
    $pdo = new PDO("pgsql:host=" . $db['host'] . ";port=" . $db['port'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

// Setup the DDS DML mapping
$container['dml'] = function($c) {
    $dds = $c['settings']['dds'];
    return require $dds['dml_mapping_file'];
};
