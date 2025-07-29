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
        ];
    }
}
