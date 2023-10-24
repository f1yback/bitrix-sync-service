<?php

use yii\redis\Connection;

return [
    'class' => Connection::class,
    'hostname' => 'localhost',
    'port' => 6379,
    'database' => 0,
];