<?php

declare(strict_types=1);

namespace app\models\dto;

use yii\base\BaseObject;

/**
 * ClientPreview DTO class
 */
class ClientPreview extends BaseObject
{
    /**
     * @var int
     */
    public int $id;
    /**
     * @var string|null
     */
    public string|null $companyName;
    /**
     * @var string|null
     */
    public string|null $subdomain;
}