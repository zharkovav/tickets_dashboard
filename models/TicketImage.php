<?php

namespace app\models;

use yii\db\ActiveRecord;

class TicketImage extends ActiveRecord
{
    public static function tableName()
    {
        return 'ticket_image';
    }

    public function rules()
    {
        return [
            [['ticket_id'], 'integer'],
            [['filename'], 'file', 'extensions' => ['png', 'jpg'], 'maxSize' => 2 * 1024 * 1024],
        ];
    }

    public function getUrl()
    {
        return 'uploads/request/' . $this->ticket_id . '/' . $this->filename;
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            @unlink(\Yii::getAlias('uploads/request/' . $this->ticket_id . '/' . $this->filename));
            return true;
        }
        return false;
    }
}
