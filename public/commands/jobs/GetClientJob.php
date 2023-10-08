<?php

declare(strict_types=1);

namespace app\commands\jobs;

use app\services\AggregatorService;
use app\services\ApiService;
use JsonException;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\httpclient\Exception;
use yii\queue\JobInterface;

/**
 * Scan Admin API background job
 */
class GetClientJob extends BaseObject implements JobInterface
{
    /**
     * @param int[] $idColumn
     * @param AggregatorService $aggregatorService
     * @param ApiService $apiService
     * @param array $config
     */
    public function __construct(
        public array $idColumn,
        public AggregatorService $aggregatorService,
        public ApiService $apiService,
        array $config = [],
    ) {
        parent::__construct($config);
    }

    /**
     * Runs job
     *
     * @param $queue
     * @return void
     * @throws JsonException
     * @throws Exception
     * @throws InvalidConfigException|\yii\db\Exception
     */
    public function execute($queue): void
    {
        $clientsInfo = $this->apiService->getClient($this->idColumn);

        $this->aggregatorService->log(
            json_encode($clientsInfo, JSON_THROW_ON_ERROR),
            'client_scan_job.log'
        );

        foreach ($clientsInfo as $client_id => $client) {
            try {
                if ($client) {
                    $clientData = json_decode($client, true, 512, JSON_THROW_ON_ERROR);
                } else {
                    throw new JsonException();
                }
            } catch (JsonException) {
                continue;
            }

            $this->aggregatorService->saveClientFromInfo(
                $client_id,
                $this->aggregatorService->createClientInfo($clientData['data'])
            );
        }
    }
}