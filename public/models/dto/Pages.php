<?php

declare(strict_types=1);

namespace app\models\dto;

use yii\base\BaseObject;

/**
 * Pages DTO class
 */
class Pages extends BaseObject
{
    /**
     * @var int
     */
    public int $status;
    /**
     * @var int
     */
    public int $total;
    /**
     * @var int
     */
    public int $page;
    /**
     * @var int
     */
    public int $perPage;
    /**
     * @var int
     */
    public int $lastPage;
}