<?php

declare(strict_types=1);

namespace app\commands\jobs;

use app\exceptions\ApiException;
use app\services\AggregatorService;
use app\services\ApiService;
use JsonException;
use yii\base\BaseObject;
use yii\httpclient\Exception;
use yii\queue\JobInterface;

/**
 * Scan Admin API background job
 */
class GetAllClientsJob extends BaseObject implements JobInterface
{
    /**
     * @param int $page
     * @param AggregatorService $aggregatorService
     * @param ApiService $apiService
     * @param array $config
     */
    public function __construct(
        public int $page,
        public AggregatorService $aggregatorService,
        public ApiService $apiService,
        array $config = [],
    )
    {
        parent::__construct($config);
    }

    /**
     * Runs job
     *
     * @param $queue
     * @return void
     * @throws JsonException
     * @throws ApiException
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function execute($queue): void
    {
        $clientPage = $this->apiService->getClients($this->page);

        $this->aggregatorService->log(
            json_encode($clientPage, JSON_THROW_ON_ERROR),
            'clients_scan_job.log'
        );

        $pageContent = $this->aggregatorService->createPageContent($clientPage['data']);

        foreach ($pageContent->data as $client) {
            $this->aggregatorService->saveClientFromPreview($client);
        }
    }
}