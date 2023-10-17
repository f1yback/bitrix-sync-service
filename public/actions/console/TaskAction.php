<?php

declare(strict_types=1);

namespace app\actions\console;

use app\services\BitrixService;
use Exception;
use yii\base\Action;

/**
 * TaskAction class
 */
class TaskAction extends Action
{
    /**
     * @param $id
     * @param $controller
     * @param BitrixService $bitrixService
     * @param array $config
     */
    public function __construct(
        $id,
        $controller,
        protected BitrixService $bitrixService,
        array $config = []
    ) {
        parent::__construct($id, $controller, $config);
    }

    /**
     * Runs action
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        if ($stuckClientsMap = $this->bitrixService->getStuckClientsMap()) {
            foreach ($stuckClientsMap as $type => $clients) {
                if (count($clients) < 50) {
                    $this->bitrixService->createBitrixSyncJob(
                        $this->bitrixService->createBatchTaskCommand($clients, $type),
                        $this->bitrixService
                    );
                } else {
                    $slices = [];
                    $iterator = 0;

                    foreach ($clients as $key => $client) {
                        $slices[$iterator][] = $client;
                        if (($key + 1) % 50 === 0) {
                            ++$iterator;
                        }
                    }

                    $batches = [];

                    foreach ($slices as $slice => $clientsSlice) {
                        $batches[$slice] = $this->bitrixService->createBatchTaskCommand($clientsSlice, $type);
                    }

                    $batches['big'] = true;
                    $this->bitrixService->createBitrixSyncJob(
                        $batches,
                        $this->bitrixService
                    );
                }
            }
        }
    }
}