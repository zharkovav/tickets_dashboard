<?php

namespace app\controllers;

use yii\web\Controller;
use Yii;
use app\models\Ticket;

class TicketsController extends Controller
{
    public function actionIndex()
    {
        $tickets = Ticket::find()->orderBy(['created_at' => SORT_DESC])->all();
        $model = new Ticket();

        return $this->render('index', [
            'tickets' => $tickets,
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $model = new Ticket();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if ($model->save()) {
                $model = Ticket::findOne($model->id);
                $ticketHtml = $this->renderPartial('_ticketItem', ['model' => $model,]);
                return [
                    'success' => true,
                    'message' => "New ticket added.",
                    'html' => $ticketHtml,
                ];
            } else {
                return ['success' => false, 'errors' => $model->getErrors()];
            }
        }

        return $this->renderAjax('_formCreate', [
            'model' => $model,
        ]);
    }

    public function actionUpdateState()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $state = Yii::$app->request->post('state');

        $model = Ticket::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'Ticket #' . $id . ' not found'];
        }

        $model->state = $state;
        if ($model->save()) {
            return ['success' => true, 'message' => 'State for ticket #' . $id . ' updated'];
        }

        return ['success' => false, 'message' => 'Error', 'errors' => $model->getErrors()];
    }

    public function actionDelete()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $model = $model = Ticket::findOne($id);

        if ($model->delete()) {
            if (Yii::$app->request->isAjax) {
                return ['success' => true, 'message' => 'Ticket #' . $id . ' deleted'];
            }
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isAjax) {
            return ['success' => false, 'message' => 'Error in: removing record'];
        }

        throw new \yii\web\ServerErrorHttpException('Error in: removing record.');
    }
}
