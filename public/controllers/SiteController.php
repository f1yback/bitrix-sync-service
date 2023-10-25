<?php

declare(strict_types=1);

namespace app\controllers;

use app\services\AggregatorService;
use app\services\ApiService;
use Exception;
use Symfony\Component\BrowserKit\Exception\JsonException;
use yii\base\Response;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class SiteController extends Controller
{
    /**
     * @param $id
     * @param $module
     * @param AggregatorService $aggregatorService
     * @param ApiService $apiService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        private AggregatorService $aggregatorService,
        private ApiService $apiService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * Default webhook endpoint
     *
     * @return Response
     * @throws Exception
     */
    public function actionIndex(): Response
    {
        $payload = $this->aggregatorService->getIncomingData();

        try {
            $id = ArrayHelper::getValue(json_decode($payload, true, 512, JSON_THROW_ON_ERROR), 'id');

            if ($id) {
                $this->aggregatorService->createGetClientJob(
                    [(int)$id],
                    $this->aggregatorService,
                    $this->apiService
                );
            }
        } catch (JsonException) {
            return $this->asJson(
                $this->aggregatorService->log('Trying to json_decode non-json value. Error. Payload was: ' . $payload, 'debug.log')
            );
        }

        return $this->asJson(
            $this->aggregatorService->log($payload, 'debug.log')
        );
    }
}
