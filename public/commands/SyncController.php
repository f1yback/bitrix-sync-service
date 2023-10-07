<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\console\GetClientAction;
use app\actions\console\GetClientsAction;
use yii\console\Controller;

/**
 * Main sync controller
 */
class SyncController extends Controller
{
    /**
     * @inheritdoc
     *
     * @return string[]
     */
    public function actions(): array
    {
        return [
            'clients' => GetClientsAction::class,
            'client' => GetClientAction::class,
        ];
    }
}