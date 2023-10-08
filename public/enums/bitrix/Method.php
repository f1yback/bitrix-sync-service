<?php

declare(strict_types=1);

namespace app\enums\bitrix;

/**
 * Bitrix24 API methods
 */
enum Method: string
{
    case UPDATE = 'crm.company.update';
    case USER_SEARCH = 'user.search';
    case BATCH = 'batch';
}