<?php

declare(strict_types=1);

namespace app\enums;

/**
 * API exception messages
 */
enum ApiExceptionMessage: string
{
    case NO_TOKEN = 'API token null response';
    case NO_SUBSCRIBE = 'Callback webhook setup error';
}