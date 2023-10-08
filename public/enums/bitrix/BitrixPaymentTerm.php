<?php

declare(strict_types=1);

namespace app\enums\bitrix;

/**
 * BitrixPaymentTerm enum class
 */
enum BitrixPaymentTerm: string
{
    case QUARTERLY = '1673';
    case ANNUAL = '1675';
    case MONTHLY = '1677';
    case SEMI_ANNUAL = '1679';
    case TWO_MONTHS = '1681';
    case FOREVER = '1683';

    /**
     * Gets assigned Bitrix24 value to admin API values
     *
     * @param int $dbPaymentTerm
     * @return string
     */
    public static function getAssigned(int $dbPaymentTerm): string
    {
        return match ($dbPaymentTerm) {
            3 => self::QUARTERLY->value,
            12 => self::ANNUAL->value,
            1 => self::MONTHLY->value,
            6 => self::SEMI_ANNUAL->value,
            2 => self::TWO_MONTHS->value,
            default => null
        };
    }
}