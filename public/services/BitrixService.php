<?php

declare(strict_types=1);

namespace app\services;

use app\commands\jobs\SyncBitrixJob;
use app\components\Bitrix;
use app\enums\bitrix\BitrixCheckoutLanguage;
use app\enums\bitrix\BitrixCompanyType;
use app\enums\bitrix\BitrixCountry;
use app\enums\bitrix\BitrixCurrency;
use app\enums\bitrix\BitrixPaymentTerm;
use app\enums\bitrix\BitrixPaymentType;
use app\enums\bitrix\Method;
use app\enums\BitrixField;
use app\enums\HttpMethod;
use app\enums\RedisKey;
use app\exceptions\ApiException;
use app\models\Client;
use app\models\dto\ClientInfo;
use app\models\Manager;
use JsonException;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\httpclient\Exception;
use yii\queue\redis\Queue;
use yii\redis\Cache;

/**
 * BitrixService class
 */
class BitrixService
{
    public const TYPE_NOT_ACTIVE = 0;
    public const TYPE_NO_ORDERS = 1;

    /**
     * @param Bitrix $bitrix
     * @param Queue $queue
     * @param Connection $connection
     * @param Cache $redis
     */
    public function __construct(
        public Bitrix $bitrix,
        private Queue $queue,
        private Connection $connection,
        private Cache $redis,
    ) {
    }

    /**
     * Gets clients array to update
     *
     * @return Client[]
     */
    public function getClientsToUpdate(): array
    {
        return Client::find()->where(['is not', 'bitrixClient', null])->asArray()->all();
    }

    /**
     * Creates DTO ClientInfo from Client model
     *
     * @param array|Client $client
     * @return ClientInfo
     */
    public function dtoFromModel(array|Client $client): ClientInfo
    {
        return new ClientInfo([
            'bitrixClient' => ClientInfo::parseBitrixClient($client['bitrixClient']),
            'url' => $client['url'],
            'pricePerUser' => $client['pricePerUser'],
            'currency' => $client['currency'],
            'paymentPeriodMonth' => $client['paymentPeriodMonth'],
            'country' => $client['country'],
            'paymentTypeId' => $client['paymentTypeId'],
            'language' => $client['language'],
            'usersCount' => $client['usersCount'],
            'managerEmail' => $client['managerEmail'],
            'logistClientId' => $client['logistClientId'],
            'lastActiveDate' => $client['lastActiveDate'],
            'lastOrderDate' => $client['lastOrderDate'],
            'licenseEndDate' => $client['licenseEndDate'],
        ]);
    }

    /**
     * Creates batch command for data update Bitrix24
     *
     * @param Client[] $clients
     * @return array
     */
    public function createBatchCommand(array $clients): array
    {
        $commands = [];

        foreach ($clients as $client) {
            $commands[$client['id']] = $this->updateCommand($this->dtoFromModel($client));
        }

        return $commands;
    }

    /**
     * Creates batch task command for data update Bitrix24
     *
     * @param Client[] $clients
     * @param int $type
     * @return array
     * @throws ApiException|\yii\db\Exception
     */
    public function createBatchTaskCommand(array $clients, int $type): array
    {
        $commands = [];

        $tx = $this->connection->beginTransaction(Transaction::SERIALIZABLE);

        foreach ($clients as $client) {
            $commands[$client->id] = $this->createTask($client, $type);
            $client->taskCreated = 1;

            if (!$client->save()) {
                $tx?->rollBack();
                throw new ApiException('Task save error. Client ID: ' . $client->id);
            }

            $this->redis->set(
                implode('', [RedisKey::CLIENT_STATUS->value, $client->bitrixClient]),
                BitrixCompanyType::STUCK->value
            );
        }

        $tx?->commit();

        return $commands;
    }

    /**
     * Parse managers from Bitrix24
     *
     * @return array
     * @throws JsonException
     * @throws ApiException
     * @throws Exception
     */
    public function parseManagers(): array
    {
        return json_decode(
            $this->bitrix->send(
                implode('', [
                    Method::USER_SEARCH->value,
                    '?',
                    http_build_query([
                        'filter' => [
                            'EMAIL' => Client::find()
                                ->select(['managerEmail'])
                                ->groupBy('managerEmail')
                                ->column()
                        ]
                    ])
                ]),
                HttpMethod::GET->value
            ),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * Saves manager to DB
     *
     * @param int $id
     * @param string $email
     * @return bool
     */
    public function saveManager(int $id, string $email): bool
    {
        if (!($manager = Manager::findOne(['email' => $email]))) {
            $manager = new Manager();
        }

        $manager->id = $id;
        $manager->email = $email;

        return $manager->save();
    }

    /**
     * Gets manager id from Bitrix24
     *
     * @param ?string $email
     * @return int|null
     */
    public function getManagerByEmail(?string $email): ?int
    {
        return $email ? Manager::findOne(['email' => $email])?->id : null;
    }

    /**
     * Creates update command for batch Bitrix24
     *
     * @param ClientInfo $clientInfo
     * @return string
     */
    private function updateCommand(ClientInfo $clientInfo): string
    {
        return implode('', [
            Method::UPDATE->value,
            '?',
            http_build_query([
                'id' => $clientInfo->bitrixClient,
                'fields' => array_merge(
                    [
                        BitrixField::BASE_NAME->value => $clientInfo->url,
                        BitrixField::PRICE_PER_USER->value => $clientInfo->pricePerUser,
                        BitrixField::PAYMENT_TERM->value => BitrixPaymentTerm::getAssigned($clientInfo->paymentPeriodMonth),
                        BitrixField::COUNTRY->value => BitrixCountry::getAssigned($clientInfo->country),
                        BitrixField::PAYMENT_TYPE->value => BitrixPaymentType::getAssigned($clientInfo->paymentTypeId),
                        BitrixField::CHECKOUT_LANGUAGE->value => BitrixCheckoutLanguage::getAssigned($clientInfo->language),
                        BitrixField::CURRENCY->value => BitrixCurrency::getAssigned($clientInfo->currency),
                        BitrixField::MANAGER->value => $this->getManagerByEmail($clientInfo->managerEmail),
                        BitrixField::USER_COUNT->value => $clientInfo->usersCount,
                        BitrixField::LAST_ACTIVE_DATE->value => $clientInfo->lastActiveDate,
                        BitrixField::LAST_ORDER_DATE->value => $clientInfo->lastOrderDate,
                        BitrixField::LICENSE_END_DATE->value => $clientInfo->licenseEndDate,
                    ],
                    $this->setClientStatus($clientInfo)
                )
            ])
        ]);
    }

    /**
     * Set client status for update action
     *
     * @param ClientInfo $clientInfo
     * @return array
     */
    private function setClientStatus(ClientInfo $clientInfo): array
    {
        if ($status = $this->redis->get(implode('', [RedisKey::CLIENT_STATUS->value, $clientInfo->bitrixClient]))) {
            $this->redis->delete(implode('', [RedisKey::CLIENT_STATUS->value, $clientInfo->bitrixClient]));
            return [BitrixField::COMPANY_TYPE->value => $status];
        }

        return strtotime($clientInfo->licenseEndDate) <= time() - 3600*24*3 ?
            [BitrixField::COMPANY_TYPE->value => BitrixCompanyType::EX_CLIENT->value] : [];
    }

    /**
     * Creates Bitrix24 sync job
     *
     * @param array $batch
     * @param BitrixService $bitrixService
     * @return void
     */
    public function createBitrixSyncJob(array $batch, BitrixService $bitrixService): void
    {
        $this->queue->push(new SyncBitrixJob($batch, $bitrixService));
    }

    /**
     * Gets all stuck [type => [client_id => manager_id]] map from db
     *
     * @return array
     * @throws \Exception
     */
    public function getStuckClientsMap(): array
    {
        return ArrayHelper::map(
            Client::find()->with(['manager'])->where(['taskCreated' => 0])->andWhere([
                'OR',
                [
                    '>=',
                    new Expression('date(now()) - interval 7 day'),
                    new Expression('date(lastActiveDate)')
                ],
                [
                    '>=',
                    new Expression('date(now()) - interval 14 day'),
                    new Expression('date(lastOrderDate)')
                ],
            ])->andWhere(['AND', ['is not', 'bitrixClient', null], ['is not', 'managerEmail', null]])->all(),
            'id',
            static fn(Client $client) => $client,
            static fn(Client $client) => $client->getMapType()
        );
    }

    /**
     * Gets task title from type
     *
     * @param int $type
     * @param string $name
     * @return string
     */
    private function getTextFromType(int $type, string $name): string
    {
        return match ($type) {
            self::TYPE_NOT_ACTIVE => "Alarm! “{$name}” не заходил в систему 7 дней",
            self::TYPE_NO_ORDERS => "Alarm! “{$name}” не создавал заказ в 4logist 14 дней",
        };
    }

    /**
     * @param Client $client
     * @param int $type
     * @return string
     */
    public function createTask(Client $client, int $type): string
    {
        return implode('', [
            Method::TASK->value,
            '?',
            http_build_query([
                'fields' => [
                    'TITLE' => $this->getTextFromType($type, $client->companyName),
                    'RESPONSIBLE_ID' => $client->manager->id,
                    'DESCRIPTION' => 'Проверить активность клиента. В случае отсутствия активности, связаться с клиентом, узнать причину и вернуть к работе.',
                    'DEADLINE' => date('Y-m-dT18:00:00', time() + 3600 * 24)
                ]
            ])
        ]);
    }
}