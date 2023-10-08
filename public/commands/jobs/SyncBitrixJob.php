<?php

declare(strict_types=1);

namespace app\commands\jobs;

use app\enums\bitrix\Method;
use app\enums\HttpMethod;
use app\exceptions\ApiException;
use app\services\BitrixService;
use JsonException;
use yii\base\BaseObject;
use yii\httpclient\Exception;
use yii\queue\JobInterface;

class SyncBitrixJob extends BaseObject implements JobInterface
{
    /**
     * @param array $batchCommands
     * @param BitrixService $bitrixService
     * @param array $config
     */
    public function __construct(
        public array $batchCommands,
        public BitrixService $bitrixService,
        array $config = [],
    ) {
        parent::__construct($config);
    }

    /**
     * Executes Bitrix24 sync job
     *
     * @param $queue
     * @return void
     * @throws JsonException
     * @throws ApiException
     * @throws Exception
     */
    public function execute($queue): void
    {
        if (!isset($this->batchCommands['big'])) {
            $this->bitrixService->bitrix->send(
                Method::BATCH->value,
                HttpMethod::GET->value,
                [
                    'halt' => 0,
                    'cmd' => $this->batchCommands
                ]
            );
        } else {
            unset($this->batchCommands['big']);
            foreach ($this->batchCommands as $batchCommand) {
                $this->bitrixService->bitrix->send(
                    Method::BATCH->value,
                    HttpMethod::GET->value,
                    [
                        'halt' => 0,
                        'cmd' => $batchCommand
                    ]
                );
                usleep(500000);
            }
        }
    }
}