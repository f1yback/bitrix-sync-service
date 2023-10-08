<?php

declare(strict_types=1);

namespace app\actions\console;

use app\services\BitrixService;
use yii\base\Action;

/**
 * Bitrix24 sync action class
 */
class BitrixAction extends Action
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
     * Runs BitrixAction
     *
     * @return void
     */
    public function run(): void
    {
        if ($clients = $this->bitrixService->getClientsToUpdate()) {
            if (count($clients) < 50) {
                $this->bitrixService->createBitrixSyncJob(
                    $this->bitrixService->createBatchCommand($clients),
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

                foreach ($slices as $slice => $clients) {
                    $batches[$slice] = $this->bitrixService->createBatchCommand($clients);
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