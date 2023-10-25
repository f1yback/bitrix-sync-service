<?php

declare(strict_types=1);

namespace app\models;

use yii\web\IdentityInterface;

/**
 * ApiUser class
 */
class ApiUser implements IdentityInterface
{
    /**
     * @var int
     */
    private static int $user = 1;

    /**
     * @inheritdoc
     *
     * @param $id
     * @return null
     */
    public static function findIdentity($id): mixed
    {
        return null;
    }

    /**
     * @inheritdoc
     *
     * @param $token
     * @param $type
     * @return null
     */
    public static function findIdentityByAccessToken($token, $type = null): mixed
    {
        return null;
    }

    /**
     * @inheritdoc
     *
     * @return int|string
     */
    public function getId(): int|string
    {
        return static::$user;
    }

    /**
     * @return int|string|null
     */
    public function getAuthKey(): int|string|null
    {
        return static::$user;
    }

    /**
     * @inheritdoc
     *
     * @param $authKey
     * @return bool
     */
    public function validateAuthKey($authKey): bool
    {
        return true;
    }
}