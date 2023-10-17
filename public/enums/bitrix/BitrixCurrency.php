<?php

declare(strict_types=1);

namespace app\enums\bitrix;

use yii\helpers\ArrayHelper;

/**
 * BitrixCurrency enum class
 */
enum BitrixCurrency: string
{
    case RUB = 'RUB';
    case USD = 'USD';
    case EUR = 'EUR';
    case UAH = 'UAH';
    case BYN = 'BYN';

    /**
     * Gets assigned currency from admin API
     *
     * @param string $currency
     * @return string|null
     */
    public static function getAssigned(string $currency): ?string
    {
        return ArrayHelper::map(self::cases(), 'name', 'value')[strtoupper($currency)] ?? null;
    }
}