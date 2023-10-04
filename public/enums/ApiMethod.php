<?php

declare(strict_types=1);

namespace app\enums;

/**
 * Available API keywords
 */
enum ApiMethod: string
{
    case TOKEN = 'token';
    case CLIENTS = 'clients';
    case SUSPEND = 'suspend';
    case SUBSCRIBE = 'clients/webhooks/subscribe';
    case UNSUBSCRIBE = 'clients/webhooks/unsubscribe';
}