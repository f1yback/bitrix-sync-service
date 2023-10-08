<?php

declare(strict_types=1);

namespace app\actions\console;

use app\exceptions\ApiException;
use app\services\BitrixService;
use JsonException;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\httpclient\Exception;

/**
 * Manager action class
 */
class ManagerAction extends Action
{
    /**
     * @param $id
     * @param $controller
     * @param BitrixService $bitrixService
     * @param array $config
     */
    public function __construct(
        $id,
        $controller,
        protected BitrixService $bitrixService,
        array $config = []
    ) {
        parent::__construct($id, $controller, $config);
    }

    /**
     * Runs Manager
     *
     * @return void
     * @throws JsonException
     * @throws ApiException
     * @throws Exception
     */
    public function run(): void
    {
        $managers = ArrayHelper::map(
            $this->bitrixService->parseManagers()['result'] ?? [],
            'ID',
            'EMAIL'
        );

        if (!empty($managers)) {
            foreach ($managers as $id => $email) {
                $this->bitrixService->saveManager((int)$id, $email);
            }
        }
    }
}