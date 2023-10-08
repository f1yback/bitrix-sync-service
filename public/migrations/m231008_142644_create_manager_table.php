<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%manager}}`.
 */
class m231008_142644_create_manager_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%manager}}', [
            'id' => $this->integer(),
            'email' => $this->string(),
        ]);

        $this->addPrimaryKey('pk-manager-id', 'manager', 'id');
        $this->createIndex('idx-manager-email', 'manager', 'email');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%manager}}');
    }
}
