<?php

declare(strict_types=1);

namespace app\services;

use app\commands\jobs\SyncBitrixJob;
use app\components\Bitrix;
use app\enums\bitrix\BitrixCheckoutLanguage;
use app\enums\bitrix\BitrixCountry;
use app\enums\bitrix\BitrixCurrency;
use app\enums\bitrix\BitrixPaymentTerm;
use app\enums\bitrix\BitrixPaymentType;
use app\enums\bitrix\Method;
use app\enums\BitrixField;
use app\enums\HttpMethod;
use app\exceptions\ApiException;
use app\models\Client;
use app\models\dto\ClientInfo;
use app\models\Manager;
use JsonException;
use yii\httpclient\Exception;
use yii\queue\redis\Queue;

/**
 * BitrixService class
 */
class BitrixService
{
    /**
     * @param Bitrix $bitrix
     * @param Queue $queue
     */
    public function __construct(
        public Bitrix $bitrix,
        private Queue $queue
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
            'bitrixClient' => $client['bitrixClient'],
            'url' => $client['url'],
            'pricePerUser' => $client['pricePerUser'],
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
     * @param string $email
     * @return int|null
     */
    public function getManagerByEmail(string $email): ?int
    {
        return Manager::findOne(['email' => $email])?->id;
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
                'fields' => [
                    BitrixField::BASE_NAME->value => $clientInfo->url,
                    BitrixField::PRICE_PER_USER->value => $clientInfo->pricePerUser,
                    BitrixField::PAYMENT_TERM->value => BitrixPaymentTerm::getAssigned($clientInfo->paymentPeriodMonth),
                    BitrixField::COUNTRY->value => BitrixCountry::getAssigned($clientInfo->country),
                    BitrixField::PAYMENT_TYPE->value => BitrixPaymentType::getAssigned($clientInfo->paymentTypeId),
                    BitrixField::CHECKOUT_LANGUAGE->value => BitrixCheckoutLanguage::getAssigned($clientInfo->language),
                    BitrixField::CURRENCY->value => BitrixCurrency::RUB->value, //TODO THIS
                    BitrixField::MANAGER->value => $this->getManagerByEmail($clientInfo->managerEmail),
                    BitrixField::USER_COUNT->value => $clientInfo->usersCount
                ]
            ])
        ]);
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
}