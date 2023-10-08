<?php

declare(strict_types=1);

namespace app\models\dto;

use yii\base\BaseObject;

/**
 * PageContent DTO class
 */
class PageContent extends BaseObject
{
    /**
     * @var ClientPreview[]
     */
    public array $data;
}