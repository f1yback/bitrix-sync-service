<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%broken_requests}}`.
 */
class m231008_075950_create_broken_requests_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%broken_requests}}', [
            'id' => $this->primaryKey(),
            'request' => $this->string(),
            'response' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP()')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%broken_requests}}');
    }
}
