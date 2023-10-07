<?php

declare(strict_types=1);

namespace app\actions\console;

use app\exceptions\ApiException;
use JsonException;
use yii\httpclient\Exception;

/**
 * Subscribe webhook action
 */
class SubscribeAction extends BaseAction
{
    /**
     * Runs subscribe action
     *
     * @return void
     * @throws ApiException
     * @throws Exception|JsonException
     */
    public function run(): void
    {
        $this->controller->stdout(print_r($this->apiService->subscribeWebhook(), true) . PHP_EOL);
    }
}