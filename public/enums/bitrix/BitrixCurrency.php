<?php

declare(strict_types=1);

namespace app\enums\bitrix;

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
}