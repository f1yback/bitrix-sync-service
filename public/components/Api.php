<?php

declare(strict_types=1);

namespace app\components;

/**
 * API component class
 */
class Api extends RequestApi
{
    public const FILE = 'api.log';

    /**
     * @var string
     */
    public string $webhookSecret;
    /**
     * @var string
     */
    public string $webhookUrl;
    /**
     * @var array
     */
    public array $auth;
}