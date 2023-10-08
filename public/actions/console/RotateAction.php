<?php

declare(strict_types=1);

namespace app\actions\console;

use app\services\RotatorService;
use yii\base\Action;

/**
 * Log rotation action
 */
class RotateAction extends Action
{
    /**
     * @param $id
     * @param $controller
     * @param RotatorService $rotatorService
     * @param array $config
     */
    public function __construct(
        $id,
        $controller,
        protected RotatorService $rotatorService,
        array $config = []
    )
    {
        parent::__construct($id, $controller, $config);
    }

    /**
     * Runs logs rotation action
     *
     * @return void
     */
    public function run(): void
    {
        $this->rotatorService->rotateLogFiles();
        $this->rotatorService->rotateBrokenRequests();
    }
}