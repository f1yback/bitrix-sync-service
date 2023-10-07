<?php

declare(strict_types=1);

namespace app\actions\console;

use app\exceptions\ApiException;
use JsonException;
use yii\httpclient\Exception;

/**
 * Gets clients from API
 */
class GetClientAction extends BaseAction
{
    /**
     * Runs get client action
     *
     * @param int $id
     * @return void
     * @throws ApiException
     * @throws Exception|JsonException
     */
    public function run(int $id): void
    {
        $this->controller->stdout(print_r($this->apiService->getClient($id), true) . PHP_EOL);
    }
}