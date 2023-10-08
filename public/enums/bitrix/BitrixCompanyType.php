<?php

declare(strict_types=1);

namespace app\enums\bitrix;

/**
 * BitrixCompanyType enum class
 */
enum BitrixCompanyType: string
{
    case CURRENT_CLIENT = 'CUSTOMER';
    case EX_CLIENT = 'UC_BK2YJD';
    case PARTNER = 'UC_EKBOF2';
    case STUCK = 'UC_ZT3M98';
}