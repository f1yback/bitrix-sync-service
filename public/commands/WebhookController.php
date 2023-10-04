<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\console\SubscribeAction;
use yii\console\Controller;

/**
 * Webhook setup controller
 */
class WebhookController extends Controller
{
    /**
     * @inheritdoc
     *
     * @return string[]
     */
    public function actions(): array
    {
        return [
            'subscribe' => SubscribeAction::class
        ];
    }
}