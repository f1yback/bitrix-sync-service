<?php

declare(strict_types=1);

namespace app\enums\bitrix;

use yii\helpers\ArrayHelper;

/**
 * BitrixCheckoutLanguage enum class
 */
enum BitrixCheckoutLanguage: string
{
    case RU = '2499';
    case LT = '2501';
    case ENG = '2503';

    /**
     * Gets assigned checkout language from admin API
     *
     * @param string $language
     * @return string|null
     */
    public static function getAssigned(string $language): ?string
    {
        return ArrayHelper::map(self::cases(), 'name', 'value')[strtoupper($language)] ?? null;
    }
}