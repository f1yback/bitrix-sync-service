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
        $idColumn = Client::find()->select('id')->column();

        $this->aggregatorService->createGetClientJob(
            $idColumn,
            $this->aggregatorService,
            $this->apiService
        );
    }
}