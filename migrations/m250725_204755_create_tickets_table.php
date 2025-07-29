<?php

use yii\db\Migration;
use app\models\State;

/**
 * Handles the creation of table `{{%tickets}}`.
 */
class m250725_204755_create_tickets_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%tickets}}');
        $this->dropTable('{{%state}}');
        $this->createTable('{{%state}}', [
            'id' => $this->primaryKey(),
            'state' => $this->string(),
        ]);

        $st = new State();
        $st->state = 'new';
        $st->save();

        $st = new State();
        $st->state = 'in_progress';
        $st->save();

        $st = new State();
        $st->state = 'done';
        $st->save();

        $this->createTable('{{%tickets}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->string(),
            'state' => $this->integer()->notNull()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk-state', 'tickets', 'state', 'state', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tickets}}');
    }
}
