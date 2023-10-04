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
     * @throws JsonException
     * @throws ApiException
     * @throws Exception
     */
    public function run()
    {
        $this->controller->stdout(print_r($this->apiService->getClients(), true) . PHP_EOL);
    }
}