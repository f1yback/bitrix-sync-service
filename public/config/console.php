<?php

use yii\debug\Module as Debug;
use yii\gii\Module as Gii;
use yii\log\FileTarget;
use yii\queue\LogBehavior;
use yii\queue\redis\Queue;
use yii\redis\Cache;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'queue'
    ],
    'controllerNamespace' => 'app\commands',
    'aliases' => array_merge(
        [
            '@bower' => '@vendor/bower-asset',
            '@npm' => '@vendor/npm-asset',
            '@tests' => '@app/tests',
        ],
        require 'aliases.php'
    ),
    'components' => [
        'queue' => [
            'class' => Queue::class,
            'as log' => LogBehavior::class,
        ],
        'cache' => [
            'class' => Cache::class,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'redis' => require 'redis.php',
    ],
    'params' => $params,
    'container' => require 'di_container.php',
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => Gii::class,
    ];
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => Debug::class,
    ];
}

return $config;
