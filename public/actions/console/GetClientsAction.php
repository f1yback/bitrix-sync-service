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
     * @throws Exception|JsonException
     */
    public function run(): void
    {
        $this->controller->stdout(print_r($this->apiService->getClients(), true) . PHP_EOL);
    }
}