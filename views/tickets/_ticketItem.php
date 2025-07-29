<?php
use yii\helpers\Html;
use app\models\State;
use yii\helpers\ArrayHelper;
?>

<tr class="ticket-row" data-id="<?= $model->id ?>" id="ticket-<?= $model->id ?>">
        <td><?= Html::encode($model->id) ?></td>
        <td><?= Html::encode($model->title) ?></td>
        <td>
            <?php
            $states = State::find()->all();
            $items = ArrayHelper::map($states,'id','state');
            ?>
            <?= Html::dropDownList(
                'state',
                $model->state,
                $items,
                [
                    'class' => 'form-control ticket-state-dropdown',
                    'data-id' => $model->id,
                ]
            ) ?>
        </td>
        <td><?= Html::encode($model->created_at) ?></td>
        <td><?= Html::button('Delete', [
            'class' => 'btn btn-danger btn-delete-ticket',
            'data-id' => $model->id,
            ]) ?>
        </td>
</tr>