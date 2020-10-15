<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%key_storage}}`.
 */
class m201015_171539_create_key_storage_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%key_storage}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(255),
            'value' => $this->text()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%key_storage}}');
    }
}
