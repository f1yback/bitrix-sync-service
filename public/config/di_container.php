<?php

use app\components\Api;
use app\components\Bitrix;
use yii\db\Connection;

$params = require 'params.php';

return [
    'definitions' => [
        Api::class => [
            'class' => Api::class,
            'url' => $params['api']['base_url'],
            'webhookSecret' => $params['api']['webhook_secret'],
            'webhookUrl' => $params['api']['webhook_url'],
            'auth' => $params['auth']
        ],
        Bitrix::class => [
            'class' => Bitrix::class,
            'url' => $params['bitrix']['webhook']
        ],
        Connection::class => require 'db.php'
    ]
];