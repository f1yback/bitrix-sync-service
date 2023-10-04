<?php

declare(strict_types=1);

namespace app\actions\console;

use app\exceptions\ApiException;
use JsonException;
use yii\httpclient\Exception;

/**
 * Subscribe webhook action
 */
class UnsubscribeAction extends BaseAction
{
    /**
     * Runs unsubscribe action
     *
     * @return void
     * @throws JsonException
     * @throws ApiException
     * @throws Exception
     */
    public function run(): void
    {
        $this->controller->stdout(print_r($this->apiService->unsubscribeWebhook(), true) . PHP_EOL);
    }
}