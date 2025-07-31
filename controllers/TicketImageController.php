<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\Response;
use app\models\TicketImage;

class TicketImageController extends Controller
{

    public function actionUpload($ticket_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $count = TicketImage::find()->where(['ticket_id' => $ticket_id])->count();
        if ($count >= 5) {
            return ['success' => false, 'error' => 'Maximum of 5 images allowed.'];
        }

        $model = new TicketImage();
        $model->ticket_id = $ticket_id;
        $file = UploadedFile::getInstanceByName('image');

        if (!$file) {
            return ['success' => false, 'error' => 'File not found.'];
        }

        $model->filename = uniqid() . '.' . $file->extension;
        $path = Yii::getAlias('@webroot/request/') . $ticket_id . '/' . $model->filename;

        if ($model->validate() && $file->saveAs($path)) {
            $model->save(false);
            return [
                'success' => true,
                'id' => $model->id,
                'url' => $model->getUrl()
            ];
        }

        return ['success' => false, 'error' => $model->getFirstError('file')];
    }

    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = TicketImage::findOne($id);
        if ($model && $model->delete()) {
            return ['success' => true];
        }

        return ['success' => false];
    }
}