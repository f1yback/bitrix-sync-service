<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\console\BitrixAction;
use app\actions\console\GetClientAction;
use app\actions\console\GetClientsAction;
use app\actions\console\ManagerAction;
use app\actions\console\TaskAction;
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
            'bitrix' => BitrixAction::class,
            'manager' => ManagerAction::class,
            'task' => TaskAction::class
        ];
    }
}