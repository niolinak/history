<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%history}}`.
 */
class m251116_184505_add_changed_attributes_column_to_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%history}}', 'changed_attributes', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%history}}', 'changed_attributes');
    }
}
