<?php

declare(strict_types=1);

namespace app\services;

use app\components\Api;
use app\enums\ApiExceptionMessage;
use app\enums\ApiMethod;
use app\enums\HttpMethod;
use app\enums\RedisKey;
use app\exceptions\ApiException;
use JsonException;
use Throwable;
use yii\base\InvalidConfigException;
use yii\httpclient\Exception;
use yii\redis\Cache;

/**
 * Admin API service class
 */
class ApiService
{
    private const STATUS_SUCCESS = 200;
    private const DEFAULT_CACHE_LIFETIME = 3500;

    /**
     * @param Cache $redis
     * @param Api $api
     * @param AggregatorService $aggregatorService
     */
    public function __construct(
        private Cache $redis,
        private Api $api,
        private AggregatorService $aggregatorService,
    ) {}

    /**
     * Gets API response or throws specified error
     *
     * @param string $data
     * @param string $message
     * @return array
     * @throws ApiException|JsonException
     */
    private function respond(string $data, string $message): array
    {
        $decoded = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        if (!empty($decoded['status']) && (int)$decoded['status'] === self::STATUS_SUCCESS) {
            return $decoded;
        }

        throw new ApiException($message);
    }

    /**
     * Gets API batch response
     *
     * @param string $data
     * @param string $request
     * @return array
     * @throws JsonException
     */
    private function batchRespond(string $data, string $request): array
    {
        $decoded = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        foreach ($decoded as $client_id => $responseElem) {
            try {
                json_decode($responseElem, true, 512, JSON_THROW_ON_ERROR);
            } catch (Throwable) {
                $this->aggregatorService->logBrokenRequest(implode('/', [$request, $client_id]), (string)$responseElem);
            }
        }

        return $decoded;
    }

    /**
     * Gets auth headers
     *
     * @return array
     */
    private function headers(): array
    {
        return [
            'Authorization' => implode(' ', ['Bearer', $this->getActualToken()])
        ];
    }

    /**
     * Gets actual token
     *
     * @return string
     */
    private function getActualToken(): string
    {
        return $this->redis->getOrSet(RedisKey::API_KEY->value, function () {
            if ($response = $this->api->send(ApiMethod::TOKEN->value, HttpMethod::POST->value, $this->api->auth)) {
                $decodedData = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
                return $decodedData['access_token'] ?? throw new ApiException(ApiExceptionMessage::NO_TOKEN->value);
            }
            throw new ApiException(ApiExceptionMessage::NO_TOKEN->value);
        }, self::DEFAULT_CACHE_LIFETIME);
    }

    /**
     * Subscribes webhook
     *
     * @return array
     * @throws ApiException
     * @throws Exception|JsonException
     */
    public function subscribeWebhook(): array
    {
        return $this->respond(
            $this->api->send(
                ApiMethod::SUBSCRIBE->value,
                HttpMethod::POST->value,
                ['url' => $this->api->webhookUrl, 'secret' => $this->api->webhookSecret],
                $this->headers()
            ),
            ApiExceptionMessage::NO_SUBSCRIBE->value
        );
    }

    /**
     * Unsubscribes webhook
     *
     * @return array
     * @throws ApiException
     * @throws Exception|JsonException
     */
    public function unsubscribeWebhook(): array
    {
        return $this->respond(
            $this->api->send(
                url: ApiMethod::UNSUBSCRIBE->value,
                type: HttpMethod::POST->value,
                headers: $this->headers()
            ),
            ApiExceptionMessage::NO_UNSUBSCRIBE->value
        );
    }

    /**
     * Gets clients
     *
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws ApiException
     * @throws Exception|JsonException
     */
    public function getClients(int $page = 1, int $perPage = 100): array
    {
        return $this->respond(
            $this->api->send(
                ApiMethod::CLIENTS->value,
                HttpMethod::GET->value,
                ['page' => $page, 'perPage' => $perPage],
                $this->headers()
            ),
            ApiExceptionMessage::NO_CLIENTS->value
        );
    }

    /**
     * Gets client by id
     *
     * @param array $idColumn
     * @return array
     * @throws Exception
     * @throws InvalidConfigException
     * @throws JsonException
     */
    public function getClient(array $idColumn): array
    {
        return $this->batchRespond(
            $this->api->batchGet(
                url: ApiMethod::CLIENTS->value,
                data: $idColumn,
                headers: $this->headers()
            ),
            ApiMethod::CLIENTS->value
        );
    }
}