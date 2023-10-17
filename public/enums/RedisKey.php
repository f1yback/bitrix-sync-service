<?php

declare(strict_types=1);

namespace app\enums;

/**
 * Redis storage keys
 */
enum RedisKey: string
{
    case API_KEY = 'api_key';
    case CLIENT_STATUS = 'client_status_';
}