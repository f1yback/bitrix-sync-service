<?php

declare(strict_types=1);

namespace app\actions\console;

use app\exceptions\ApiException;
use JsonException;
use yii\httpclient\Exception;

/**
 * Gets clients from API
 */
class GetClientsAction extends BaseAction
{
    /**
     * Runs get client action
     *
     * @return void
     * @throws ApiException
     * @throws Exception
     * @throws JsonException
     */
    public function run(): void
    {
        $currentPage = 1;

        $clientsResponse = $this->apiService->getClients($currentPage);

        $pages = $this->aggregatorService->createPages($clientsResponse);

        for ($i = $currentPage; $i <= $pages->lastPage; $i++) {
            $this->aggregatorService->createGetAllClientsJob($i, $this->aggregatorService, $this->apiService);
        }
    }
}