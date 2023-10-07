<?php

declare(strict_types=1);

namespace app\services;

use Yii;

/**
 * Data aggregation service
 */
class AggregatorService
{
    /**
     * Decorates log data
     *
     * @param string $data
     * @return string
     */
    private function decorate(string $data): string
    {
        return implode(PHP_EOL, [
            date('d.m.Y H:i:s'),
            '-----------------',
            $data,
            '-----------------',
            PHP_EOL.PHP_EOL
        ]);
    }

    /**
     * Make debug log
     *
     * @param string $data
     * @param string $file
     * @return bool|int
     */
    public function log(string $data, string $file): bool|int
    {
        if ($data) {
            return file_put_contents(implode('/', [
                Yii::getAlias('@logs'),
                $file
            ]), $this->decorate($data), FILE_APPEND);
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