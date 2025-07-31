<?php

namespace app\models;

use yii\db\ActiveRecord;

class Ticket extends ActiveRecord
{
    public static function tableName()
    {
        return 'tickets';
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
        ];
    }

    public function getComments()
    {
        return $this->hasMany(Comment::class, ['ticket_id' => 'id'])->orderBy(['created_at' => SORT_ASC]);
    }

    public function getImages()
    {
        return $this->hasMany(TicketImage::class, ['ticket_id' => 'id']);
    }
}
