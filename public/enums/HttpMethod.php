<?php

declare(strict_types=1);

namespace app\enums;

/**
 * Available Http methods
 */
enum HttpMethod: string
{
    case POST = 'POST';
    case GET = 'GET';
}