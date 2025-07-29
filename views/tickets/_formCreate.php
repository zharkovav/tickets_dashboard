<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin([
    'id' => 'ticket-form',
    'enableAjaxValidation' => false,
]); ?>

<?= $form->field($model, 'title')->textInput() ?>
<?= $form->field($model, 'description')->textarea() ?>

<div class="form-group">
    <?= Html::submitButton('Create', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
