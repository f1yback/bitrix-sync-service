<?php

declare(strict_types=1);

namespace app\models\dto;

use ArrayAccess;
use yii\base\BaseObject;

/**
 * ClientInfo DTO class
 */
class ClientInfo extends BaseObject implements ArrayAccess
{
    /**
     * @var int|null
     */
    public int|null $bitrixClient;
    /**
     * @var string|null
     */
    public string|null $url;
    /**
     * @var int|null
     */
    public int|null $pricePerUser;
    /**
     * @var int|null
     */
    public int|null $paymentPeriodMonth;
    /**
     * @var string|null
     */
    public string|null $country;
    /**
     * @var int|null
     */
    public int|null $paymentTypeId;
    /**
     * @var string|null
     */
    public string|null $language;
    /**
     * @var int|null
     */
    public int|null $usersCount;
    /**
     * @var string|null
     */
    public string|null $managerEmail;
    /**
     * @var int|null
     */
    public int|null $logistClientId;
    /**
     * @var string|null
     */
    public string|null $lastActiveDate;
    /**
     * @var string|null
     */
    public string|null $lastOrderDate;
    /**
     * @var string|null
     */
    public string|null $licenseEndDate;

    /**
     * @inheritdoc
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->$offset);
    }

    /**
     * @inheritdoc
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->$offset;
    }

    /**
     * @inheritdoc
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->$offset = $value;
    }

    /**
     * @inheritdoc
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->$offset = null;
    }
}