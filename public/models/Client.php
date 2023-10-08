<?php

declare(strict_types=1);

namespace app\models;

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
 * @property int|null $paymentPeriodMonth
 * @property string|null $country
 * @property int|null $paymentTypeId
 * @property string|null $language
 * @property int|null $usersCount
 * @property string|null $managerEmail
 * @property int|null $logistClientId
 * @property string|null $lastActiveDate
 * @property string|null $lastOrderDate
 * @property string|null $licenseEndDate
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
            [['id', 'bitrixClient', 'paymentPeriodMonth', 'paymentTypeId', 'usersCount', 'logistClientId'], 'integer'],
            [['pricePerUser'], 'number'],
            [['lastActiveDate', 'lastOrderDate', 'licenseEndDate'], 'safe'],
            [['companyName', 'subdomain', 'url', 'language', 'managerEmail'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 10],
            [['id'], 'unique'],
        ];
    }
}
