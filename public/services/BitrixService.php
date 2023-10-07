<?php

declare(strict_types=1);

namespace app\services;

use app\components\Bitrix;

/**
 * BitrixService class
 */
class BitrixService
{
    /**
     * @param Bitrix $bitrix
     */
    public function __construct(private Bitrix $bitrix)
    {}
}