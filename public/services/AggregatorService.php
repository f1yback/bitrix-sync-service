<?php

declare(strict_types=1);

namespace app\services;

use app\commands\jobs\GetAllClientsJob;
use app\commands\jobs\GetClientJob;
use app\models\BrokenRequests;
use app\models\Client;
use app\models\dto\ClientInfo;
use app\models\dto\ClientPreview;
use app\models\dto\PageContent;
use app\models\dto\Pages;
use Yii;
use yii\db\Connection;
use yii\db\Exception;
use yii\db\Transaction;
use yii\queue\redis\Queue;

/**
 * Data aggregation service
 */
class AggregatorService
{
    /**
     * @param Queue $queue
     * @param Connection $connection
     */
    public function __construct(private Queue $queue, private Connection $connection)
    {
    }

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
            PHP_EOL . PHP_EOL
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
            return file_put_contents(
                implode('/', [
                    Yii::getAlias('@logs'),
                    $file
                ]),
                $this->decorate($data),
                FILE_APPEND
            );
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

    /**
     * Creates Pages DTO from API response
     *
     * @param array $apiResponse
     * @return Pages
     */
    public function createPages(array $apiResponse): Pages
    {
        return new Pages([
            'status' => $apiResponse['status'],
            'total' => $apiResponse['total'],
            'page' => $apiResponse['page'],
            'perPage' => $apiResponse['perPage'],
            'lastPage' => $apiResponse['lastPage'],
        ]);
    }

    /**
     * Creates PageContent DTO from API response
     *
     * @param array $clientsPreview
     * @return PageContent
     */
    public function createPageContent(array $clientsPreview): PageContent
    {
        $clients = [];

        foreach ($clientsPreview as $client) {
            $clients[] = new ClientPreview([
                'id' => $client['id'],
                'companyName' => $client['companyName'],
                'subdomain' => $client['subdomain'],
            ]);
        }

        return new PageContent(['data' => $clients]);
    }

    /**
     * Creates ClientInfo DTO from API response
     *
     * @param array $clientData
     * @return ClientInfo
     */
    public function createClientInfo(array $clientData): ClientInfo
    {
        return new ClientInfo([
            'bitrixClient' => ClientInfo::parseBitrixClient($clientData['bitrixClient']),
            'url' => $clientData['url'],
            'pricePerUser' => $clientData['pricePerUser'],
            'currency' => $clientData['currency']['code'] ?? null,
            'paymentPeriodMonth' => $clientData['paymentPeriodMonth'],
            'country' => $clientData['country'],
            'paymentTypeId' => $clientData['paymentTypeId'],
            'language' => $clientData['language'],
            'usersCount' => $clientData['usersCount'],
            'managerEmail' => $clientData['managerEmail'],
            'logistClientId' => $clientData['4logistClientId'],
            'lastActiveDate' => $clientData['lastActiveDate'],
            'lastOrderDate' => $clientData['lastOrderDate'],
            'licenseEndDate' => $clientData['licenseEndDate'],
        ]);
    }

    /**
     * Creates API scan job
     *
     * @param int $page
     * @param AggregatorService $aggregatorService
     * @param ApiService $apiService
     * @return void
     */
    public function createGetAllClientsJob(
        int $page,
        AggregatorService $aggregatorService,
        ApiService $apiService
    ): void {
        $this->queue->push(new GetAllClientsJob($page, $aggregatorService, $apiService));
    }

    /**
     * Creates specified client API scan job
     *
     * @param int[] $idColumn
     * @param AggregatorService $aggregatorService
     * @param ApiService $apiService
     * @return void
     */
    public function createGetClientJob(
        array $idColumn,
        AggregatorService $aggregatorService,
        ApiService $apiService
    ): void {
        $this->queue->push(new GetClientJob($idColumn, $aggregatorService, $apiService));
    }

    /**
     * Finds client from ClientPreview DTO
     *
     * @param ClientPreview $clientPreview
     * @return Client|null
     */
    public function findClientFromPreview(ClientPreview $clientPreview): ?Client
    {
        return Client::findOne($clientPreview->id);
    }

    /**
     * Save client from ClientPreview DTO
     *
     * @param ClientPreview $clientPreview
     * @return bool
     * @throws Exception
     */
    public function saveClientFromPreview(ClientPreview $clientPreview): bool
    {
        $transaction = $this->connection->beginTransaction(Transaction::SERIALIZABLE);

        if (!($client = $this->findClientFromPreview($clientPreview))) {
            $client = new Client();
        }

        $client->setAttributes([
            'id' => $clientPreview->id,
            'subdomain' => $clientPreview->subdomain,
            'companyName' => $clientPreview->companyName
        ]);

        if (!$client->save()) {
            $transaction?->rollBack();

            return false;
        }

        $transaction?->commit();

        return true;
    }

    /**
     * Saves client info from ClientInfo DTO
     *
     * @param int $id
     * @param ClientInfo $clientInfo
     * @return bool
     * @throws Exception
     */
    public function saveClientFromInfo(int $id, ClientInfo $clientInfo): bool
    {
        $transaction = $this->connection->beginTransaction(Transaction::SERIALIZABLE);

        if (($client = Client::findOne($id))) {
            $client->setAttributes((array)$clientInfo);

            if ($client->save()) {
                $transaction?->commit();
                return true;
            }

            $this->log(json_encode($client->errors), 'client.log');
        }

        $transaction?->rollBack();

        return false;
    }

    /**
     * Save broken request
     *
     * @param string $request
     * @param string $response
     * @return bool
     */
    public function logBrokenRequest(string $request, string $response): bool
    {
        return (new BrokenRequests([
            'request' => $request,
            'response' => $response,
        ]))->save();
    }
}