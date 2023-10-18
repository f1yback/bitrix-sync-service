<?php

declare(strict_types=1);

namespace app\components;

use app\enums\HttpMethod;
use app\exceptions\ApiException;
use app\services\AggregatorService;
use JsonException;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception;
use yii\httpclient\Request;

/**
 * Base RequestApi component class
 */
abstract class RequestApi extends BaseObject
{
    /**
     * @var string
     */
    public string $url;
    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @param Client $client
     * @param AggregatorService $aggregatorService
     * @param array $config
     * @throws InvalidConfigException
     */
    public function __construct(
        protected Client $client,
        protected AggregatorService $aggregatorService,
        array $config = []
    ) {
        parent::__construct($config);

        $this->client->baseUrl = $this->url;
        $this->client->setTransport(CurlTransport::class);
        $this->request = $this->client->createRequest();
    }

    /**
     * Send request to API endpoint
     *
     * @param string $url
     * @param string $type
     * @param array $data
     * @param array|null $headers
     * @return string|null
     * @throws Exception
     * @throws JsonException
     * @throws ApiException
     */
    public function send(string $url, string $type, array $data = [], array $headers = null): ?string
    {
        $request = $this->request->setUrl($url)->setMethod($type);

        if ($type === HttpMethod::POST->value) {
            $request->setData($data)->setFormat(Client::FORMAT_JSON);
        } else {
            $request->setUrl(
                implode('', [
                    $url,
                    '?',
                    http_build_query($data)
                ])
            );
        }

        if ($headers) {
            $request->setHeaders($headers);
        }

        $response = $request->send();

        $this->aggregatorService->log(
            implode(PHP_EOL, [
                "[requestUrl]: $url",
                "[type]: $type",
                '[payload]: ' . json_encode($data, JSON_THROW_ON_ERROR),
                '[statusCode]: ' . $response->getStatusCode(),
                '[content]: ' . PHP_EOL . $response->getContent(),
            ]),
            static::FILE
        );

        if ($response->getStatusCode() === '200') {
            return $response->getContent();
        }

        throw new ApiException("Error. Response status code is {$response->statusCode}");
    }

    /**
     * Batch GET from API
     *
     * @param string $url
     * @param array $data
     * @param array|null $headers
     * @return bool|string
     * @throws Exception
     * @throws InvalidConfigException
     * @throws JsonException
     */
    public function batchGet(string $url, array $data = [], array $headers = null): bool|string
    {
        $batch = [];

        foreach ($data as $id) {
            $batch[$id] = $this->client->get(implode('/', [
                $url,
                $id
            ]));

            if ($headers) {
                $batch[$id]->setHeaders($headers);
            }
        }

        $responses = $this->client->batchSend($batch);

        $responseContent = [];

        foreach ($responses as $id => $response) {
            $responseContent[$id] = $response->getContent();
            /*$this->aggregatorService->log(
                implode(PHP_EOL, [
                    "[requestUrl]: $url",
                    '[type]: ' . HttpMethod::GET->value,
                    '[payload]: ' . $id,
                    '[statusCode]: ' . $response->getStatusCode(),
                    '[content]: ' . PHP_EOL . $response->getContent(),
                ]),
                static::FILE
            );

            if ($response->getStatusCode() === '200') {
                $responseContent[$id] = $response->getContent();
            } else {
                $responseContent[$id] = false;
            }*/
        }

        return json_encode($responseContent, JSON_THROW_ON_ERROR);
    }
}