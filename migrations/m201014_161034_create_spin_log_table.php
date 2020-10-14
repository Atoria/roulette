<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%spin_log}}`.
 */
class m201014_161034_create_spin_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%spin_log}}', [
            'id' => $this->primaryKey(),
            'status' => $this->string(255),
            'winning_number' => $this->decimal(20, 2),
            'won_amount' => $this->decimal(20, 2),
            'user_ip' => $this->string(255),
            'created_by' => $this->integer(11),
            'created_at' => $this->integer(11)
        ]);

        $this->addForeignKey('FK_spin_log_created_by',
            '{{%spin_log}}',
            'created_by',
            '{{%user}}',
            'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK_spin_log_created_by', '{{%spin_log}}');
        $this->dropTable('{{%spin_log}}');
    }
}
