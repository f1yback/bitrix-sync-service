<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "manager".
 *
 * @property int $id
 * @property string|null $email
 */
class Manager extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'manager';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['email'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }
}
