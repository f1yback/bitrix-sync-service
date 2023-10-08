<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "broken_requests".
 *
 * @property int $id
 * @property string|null $request
 * @property string|null $response
 * @property string $created_at
 */
class BrokenRequests extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'broken_requests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['response'], 'string'],
            [['created_at'], 'safe'],
            [['request'], 'string', 'max' => 255],
        ];
    }
}
