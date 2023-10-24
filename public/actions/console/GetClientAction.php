<?php

declare(strict_types=1);

namespace app\actions\console;

use app\models\Client;

/**
 * Gets clients from API
 */
class GetClientAction extends BaseAction
{
    /**
     * Runs get client action
     *
     * @return void
     */
    public function run(): void
    {
        $clientsCount = Client::find()->count();

        for ($i = 0; $i < $clientsCount; $i+=100) {
            $idColumn = Client::find()->offset($i)->limit(100)->select('id')->column();

            $this->aggregatorService->createGetClientJob(
                $idColumn,
                $this->aggregatorService,
                $this->apiService
            );
        }
    }
}