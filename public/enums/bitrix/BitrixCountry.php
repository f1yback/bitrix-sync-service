<?php

declare(strict_types=1);

namespace app\enums\bitrix;

use yii\helpers\ArrayHelper;

/**
 * BitrixCountry enum class
 */
enum BitrixCountry: string
{
    case ARM = '991';
    case AZ = '987';
    case BG = '989';
    case BY = '963';
    //case CH = '';
    case CN = '999';
    //case CYP = '';
    case DE = '1013';
    case ES = '1001';
    case EST = '981';
    case FI = '993';
    case GEO = '977';
    case ISR = '1431';
    case KGZ = '1009';
    //case KOR = '';
    //case OTH = '';
    case KZ = '973';
    case LT = '965';
    case LV = '979';
    case MDA = '995';
    case MK = '997';
    case PL = '971';
    case RU = '967';
    case SE = '1003';
    case TR = '1007';
    case TJ = '983';
    case TM = '985';
    case TH = '1005';
    case UA = '969';
    case USA = '1011';
    case UZB = '975';

    /**
     * Gets assigned country from admin API
     *
     * @param string $country
     * @return string|null
     */
    public static function getAssigned(string $country): ?string
    {
        return ArrayHelper::map(self::cases(), 'name', 'value')[strtoupper($country)] ?? null;
    }
}