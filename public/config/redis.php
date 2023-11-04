<?php

use yii\redis\Connection;

return [
    'class' => Connection::class,
    'hostname' => 'bitrix-sync-service-redis',
    'port' => 6379,
    'database' => 0,
];