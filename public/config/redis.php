<?php

use yii\redis\Connection;

return [
    'class' => Connection::class,
    'hostname' => 'bx-sync-service-redis',
    'port' => 6379,
    'database' => 0,
];