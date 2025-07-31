<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ticket_images}}`.
 */
class m250731_210215_create_table_images_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('ticket_image', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer()->notNull(),
            'filename' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey('fk_ticket_image_task', 'ticket_image', 'ticket_id', 'tickets', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ticket_image}}');
    }
}
