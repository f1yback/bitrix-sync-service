<?php

use yii\symfonymailer\Mailer;
use yii\symfonymailer\Message;
use yii\queue\LogBehavior;
use yii\queue\redis\Queue;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => array_merge(
        [
            '@bower' => '@vendor/bower-asset',
            '@npm' => '@vendor/npm-asset',
            '@tests' => '@app/tests',
        ],
        require 'aliases.php'
    ),
    'language' => 'en-US',
    'components' => [
        'queue' => [
            'class' => Queue::class,
            'as log' => LogBehavior::class,
        ],
        'db' => $db,
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
            'messageClass' => Message::class
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ],
        'redis' => require 'redis.php',
    ],
    'params' => $params,
];
