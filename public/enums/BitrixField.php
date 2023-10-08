<?php

declare(strict_types=1);

namespace app\enums;

/**
 * Bitrix fields
 */
enum BitrixField: string
{
    case BASE_NAME = 'UF_CRM_63C67E38B78A6';
    case PRICE_PER_USER = 'UF_CRM_A18';
    case PAYMENT_TERM = 'UF_CRM_A24';
    case PAYMENT_TYPE = 'UF_CRM_1688452014095';
    case COUNTRY = 'UF_CRM_63C67E38E0AF6';
    case CHECKOUT_LANGUAGE = 'UF_CRM_1688453815003';
    case CURRENCY = 'CURRENCY';
    case MANAGER = 'ASSIGNED_BY_ID';
    case USER_COUNT = 'UF_CRM_63C67E38A75B7';
    case COMPANY_TYPE = 'COMPANY_TYPE';
}