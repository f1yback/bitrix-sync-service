<?php

declare(strict_types=1);

namespace app\services;

use app\enums\ApiExceptionMessage;
use app\enums\ApiMethod;
use app\enums\HttpMethod;
use app\enums\RedisKey;
use app\exceptions\ApiException;
use JsonException;
use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\httpclient\Request;
use yii\redis\Cache;

/**
 * Admin API service class
 */
class ApiService
{
    private const STATUS_SUCCESS = 200;
    private const DEFAULT_CACHE_LIFETIME = 3500;
    private const BASE_URL = 'https://signadmi789.4logist.com/api/';
    private const WEBHOOK_SECRET = 'x40rlo1s';
    private const WEBHOOK_URL = 'https://210713.fornex.cloud/';
    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var array
     */
    private array $authParams;

    /**
     * @param Client $client
     * @param Cache $redis
     * @throws InvalidConfigException
     */
    public function __construct(
        private Client $client,
        private Cache $redis
    ) {
        $this->client->baseUrl = self::BASE_URL;
        $this->request = $this->client->createRequest();
        $this->authParams = Yii::$app->params['auth'];
    }

    /**
     * Send data to API
     *
     * @param string $url
     * @param string $type
     * @param array $data
     * @param array|null $headers
     * @return string|null
     * @throws Exception
     */
    private function send(string $url, string $type, array $data = [], array $headers = null): ?string
    {
        $request = $this->request->setUrl($url)->setMethod($type)->setData($data)->setFormat(Client::FORMAT_JSON);

        if ($headers) {
            $request->setHeaders($headers);
        }

        return $request->send()->getContent();
    }

    /**
     * Gets API response or throws specified error
     *
     * @param string $data
     * @param string $message
     * @return mixed
     * @throws ApiException
     * @throws JsonException
     */
    private function respond(string $data, string $message)
    {
        $decoded = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        if (!empty($decoded) && (int)$decoded['status'] === self::STATUS_SUCCESS) {
            return $decoded;
        }

        throw new ApiException($message);
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
            if ($response = $this->send(ApiMethod::TOKEN->value, HttpMethod::POST->value, $this->authParams)) {
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
     * @throws Exception
     * @throws JsonException
     */
    public function subscribeWebhook(): array
    {
        return $this->respond(
            $this->send(
                ApiMethod::SUBSCRIBE->value,
                HttpMethod::POST->value,
                ['url' => self::WEBHOOK_URL, 'secret' => self::WEBHOOK_SECRET],
                $this->headers()
            ),
            ApiExceptionMessage::NO_SUBSCRIBE->value
        );
    }

    public function getClients(int $page = 1, int $perPage = 1000)
    {
        return $this->respond(
            $this->send(
                ApiMethod::CLIENTS->value,
                HttpMethod::GET->value,
                ['page' => $page, 'perPage' => $perPage],
                $this->headers()
            ),
            ApiExceptionMessage::NO_SUBSCRIBE->value
        );
    }
}