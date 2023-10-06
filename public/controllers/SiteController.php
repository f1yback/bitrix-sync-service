<?php

declare(strict_types=1);

namespace app\controllers;

use app\services\AggregatorService;
use yii\base\Response;
use yii\rest\Controller;

class SiteController extends Controller
{
    /**
     * @param $id
     * @param $module
     * @param AggregatorService $aggregatorService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        private AggregatorService $aggregatorService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * Default webhook endpoint
     *
     * @return Response
     */
    public function actionIndex(): Response
    {
        return $this->asJson($this->aggregatorService->log($this->aggregatorService->getIncomingData()));
    }
}
