<?php

use yii\db\Migration;

/**
 * Class m230706_110212_history
 */
class m230706_110212_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230706_110212_history cannot be reverted.\n";

        return false;
    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('history',[
            'id'=>$this->primaryKey(),
            'table_name' => $this->text()->notNull(),
            'record_id' => $this->integer()->notNull(),
            'change_date' => $this->dateTime()->notNull(),
            'user_id' => $this->integer()->null(),
            'changes' => $this->text()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('history');
    }

}
