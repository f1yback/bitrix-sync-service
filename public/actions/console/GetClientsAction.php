<?php

declare(strict_types=1);

namespace app\actions\console;

/**
 * Gets clients from API
 */
class GetClientsAction extends BaseAction
{
    /**
     * Runs get client action
     *
     * @return void
     */
    public function run()
    {
        $this->controller->stdout(print_r($this->apiService->getClients(), true) . PHP_EOL);
    }
}