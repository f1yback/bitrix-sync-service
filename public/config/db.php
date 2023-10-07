<?php

use yii\db\Connection;

return [
    'class' => Connection::class,
    'dsn' => 'mysql:host=bx-sync-service-mysql;dbname=db',
    'username' => 'user',
    'password' => 'pass',
    'charset' => 'utf8',
];
