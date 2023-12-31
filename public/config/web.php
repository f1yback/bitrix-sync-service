<?php

use app\models\ApiUser;
use yii\caching\CacheInterface;
use yii\debug\Module as Debug;
use yii\gii\Module as Gii;
use yii\log\FileTarget;
use yii\redis\Cache;
use yii\symfonymailer\Mailer;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => array_merge(
        [
            '@bower' => '@vendor/bower-asset',
            '@npm' => '@vendor/npm-asset',
        ],
        require 'aliases.php'
    ),
    'components' => [
        'request' => [
            'cookieValidationKey' => '0U1t-qNe1ssP6kMVYFxVFicjClJcCyeB',
        ],
        'cache' => [
            'class' => Cache::class,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'user' => [
            'identityClass' => ApiUser::class
        ],
        'db' => $db,
        'redis' => require 'redis.php',
    ],
    'container' => array_merge([
            'definitions' => [
                CacheInterface::class => [
                    'class' => Cache::class
                ]
            ]
        ],
        require 'di_container.php'
    ),
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => Debug::class,
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => Gii::class,
        'allowedIPs' => ['*']
    ];
}

return $config;
