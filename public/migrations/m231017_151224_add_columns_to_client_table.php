<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%client}}`.
 */
class m231017_151224_add_columns_to_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('client', 'isActive', $this->tinyInteger()->defaultValue(1));
        $this->addColumn('client', 'taskCreated', $this->tinyInteger()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('client', 'isActive');
        $this->dropColumn('client', 'taskCreated');
    }
}
