<?php

declare(strict_types=1);

namespace app\services;

/**
 * Data aggregation service
 */
class AggregatorService
{
    /**
     * Make debug log
     *
     * @param $data
     * @return bool|int
     */
    public function log($data): bool|int
    {
        if ($data) {
            return file_put_contents('debug.log', $data . PHP_EOL, FILE_APPEND);
        }

        return false;
    }

    /**
     * Gets incoming data
     *
     * @return bool|string
     */
    public function getIncomingData(): bool|string
    {
        return file_get_contents('php://input');
    }
}