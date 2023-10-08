<?php

declare(strict_types=1);

namespace app\actions\console;

use app\services\AggregatorService;
use app\services\ApiService;
use yii\base\Action;

/**
 * Base console action
 */
abstract class BaseAction extends Action
{
    /**
     * @param $id
     * @param $controller
     * @param ApiService $apiService
     * @param AggregatorService $aggregatorService
     * @param array $config
     */
    public function __construct(
        $id,
        $controller,
        protected ApiService $apiService,
        protected AggregatorService $aggregatorService,
        array $config = []
    )
    {
        parent::__construct($id, $controller, $config);
    }
}