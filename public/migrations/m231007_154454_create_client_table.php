<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m231007_154454_create_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client}}', [
            'id' => $this->integer(),
            'companyName' => $this->string(),
            'subdomain' => $this->string(),
            'bitrixClient' => $this->integer(),
            'url' => $this->string(),
            'pricePerUser' => $this->decimal(),
            'paymentPeriodMonth' => $this->integer(),
            'country' => $this->string(10),
            'paymentTypeId' => $this->integer(),
            'language' => $this->string(),
            'usersCount' => $this->integer(),
            'managerEmail' => $this->string(),
            'logistClientId' => $this->integer(),
            'lastActiveDate' => $this->date(),
            'lastOrderDate' => $this->date(),
            'licenseEndDate' => $this->date(),
        ]);

        $this->addPrimaryKey('pk-client-id', 'client', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client}}');
    }
}
