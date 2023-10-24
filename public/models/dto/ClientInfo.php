<?php

declare(strict_types=1);

namespace app\models\dto;

use ArrayAccess;
use Yii;
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
     * @var float|null
     */
    public float|null $pricePerUser;
    /**
     * @var string|null
     */
    public string|null $currency;
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

    /**
     * Gets parsed bitrix client from admin API response
     *
     * @param string|int|null $bitrixClient
     * @return int|null
     */
    public static function parseBitrixClient(string|int|null $bitrixClient): ?int
    {
        if ((is_string($bitrixClient) && is_numeric($bitrixClient)) || is_int($bitrixClient)) {
            return (int)$bitrixClient;
        }
        if (is_string($bitrixClient) && str_contains($bitrixClient, Yii::$app->params['bitrix']['domain'])) {
            $parsedUrlArray = array_filter(explode('/', $bitrixClient), static fn($elem) => !empty($elem));
            $parsedId = array_pop($parsedUrlArray);
            if (is_numeric($parsedId) && (int)$parsedId !== 0) {
                return (int)$parsedId;
            }
        }
        return null;
    }
}