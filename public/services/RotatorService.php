<?php

declare(strict_types=1);

namespace app\services;

use app\models\BrokenRequests;
use Yii;
use yii\db\Expression;

/**
 * RotatorService class
 */
class RotatorService
{
    /**
     * Removes all old logs
     *
     * @return void
     */
    public function rotateBrokenRequests(): void
    {
        BrokenRequests::deleteAll(new Expression('NOW() - INTERVAL 24 HOUR > created_at'));
    }

    /**
     * Cleans log files which filesize exceed 10 Mb
     *
     * @return void
     */
    public function rotateLogFiles(): void
    {
        $dir = Yii::getAlias('@logs');

        $files = array_filter(scandir($dir), static fn($elem) => !str_starts_with($elem, '.'));

        if ($files) {
            foreach ($files as $file) {
                $fullPath = implode('/', [$dir, $file]);

                if (file_exists($fullPath) && ((filesize($fullPath) / 1024) / 1024) > 10) {
                    file_put_contents($fullPath, '');
                }
            }
        }
    }
}