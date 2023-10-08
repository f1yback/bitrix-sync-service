<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\console\RotateAction;
use yii\console\Controller;

/**
 * LogController class
 */
class LogController extends Controller
{
    /**
     * @inheritdoc
     *
     * @return array
     */
    public function actions(): array
    {
        return [
            'rotate' => RotateAction::class
        ];
    }
}