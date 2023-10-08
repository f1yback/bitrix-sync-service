<?php

declare(strict_types=1);

namespace app\enums\bitrix;

/**
 * BitrixPaymentType enum class
 */
enum BitrixPaymentType: string
{
    case CARD = '2495';
    case ACCOUNT = '2497';

    /**
     * Gets assigned payment type value from admin API
     *
     * @param int $paymentType
     * @return string
     */
    public static function getAssigned(int $paymentType): string
    {
        return match ($paymentType) {
            2 => self::ACCOUNT->value,
            default => self::CARD->value
        };
    }
}