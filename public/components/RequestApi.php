<?php

declare(strict_types=1);

namespace app\components;

use app\enums\HttpMethod;
use app\exceptions\ApiException;
use app\services\AggregatorService;
use JsonException;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\httpclient\Request;

/**
 * Base RequestApi component class
 */
abstract class RequestApi extends Component
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
    )
    {
        parent::__construct($config);

        $this->client->baseUrl = $this->url;
        $this->request = $this->client->createRequest();
    }

    /**
     * Request data
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
            $request->setUrl("{$url}?" . http_build_query($data));
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
            ]), static::FILE
        );

        if($response->getStatusCode() === '200') {
            return $response->getContent();
        }

        throw new ApiException("Error. Response status code is {$response->statusCode}");
    }
}