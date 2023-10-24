<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client".
 *
 * @property int $id
 * @property string|null $companyName
 * @property string|null $subdomain
 * @property int|null $bitrixClient
 * @property string|null $url
 * @property float|null $pricePerUser
 * @property string|null $currency
 * @property int|null $paymentPeriodMonth
 * @property string|null $country
 * @property int|null $paymentTypeId
 * @property string|null $language
 * @property int|null $usersCount
 * @property string|null $managerEmail
 * @property int|null $logistClientId
 * @property int|null $taskCreated
 * @property int|null $isActive
 * @property string|null $lastActiveDate
 * @property string|null $lastOrderDate
 * @property string|null $licenseEndDate
 *
 * @property Manager $manager
 */
class Client extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'required'],
            [['id', 'bitrixClient', 'paymentPeriodMonth', 'paymentTypeId', 'usersCount', 'logistClientId', 'isActive', 'taskCreated'], 'integer'],
            [['pricePerUser'], 'number'],
            [['lastActiveDate', 'lastOrderDate', 'licenseEndDate'], 'safe'],
            [['companyName', 'subdomain', 'url', 'language', 'managerEmail', 'currency'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 10],
            [['id'], 'unique'],
        ];
    }

    /**
     * Gets manager assigned to client
     *
     * @return ActiveQuery
     */
    public function getManager(): ActiveQuery
    {
        return $this->hasOne(Manager::class, ['email' => 'managerEmail']);
    }

    /**
     * Gets map type
     *
     * @return int
     */
    public function getMapType(): int
    {
        return (int)($this->lastActiveDate <= date('Y-m-d', time() - 3600 * 24 * 7));
    }
}
