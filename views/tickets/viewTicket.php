<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\State;
use app\models\Comment;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$this->title = $ticket->title;
?>

<p>
    <?= Html::a('← Back to tickets list', ['index'], ['class' => 'btn btn-secondary']) ?>
</p>

<h1>View for ticket #<?= Html::encode($ticket->id) ?>: <?= Html::encode($ticket->title) ?></h1>

<p><?= Html::encode($ticket->description) ?></p>
<table class="table" id="ticket-table">
    <thead></thead>
    <tbody>
        <tr>
            <th>Ticket ID</th>
            <td><?= Html::encode($ticket->id) ?></td>
        </tr>
        <tr>
            <th>Title</th>
            <td><?= Html::encode($ticket->title) ?></td>
        </tr>
        <tr>
            <th>Description</th>
            <td><?= Html::encode($ticket->description) ?></td>
        </tr>
        <tr>
            <th>State</th>
            <td>
                <?php $states = State::find()->all();
                $items = ArrayHelper::map($states,'id','state');
                ?>
                <?= Html::encode($items[$ticket->state]) ?>
            </td>
        </tr>
        <tr>
            <th>Created at</th>
            <td><?= Html::encode($ticket->created_at) ?></td>
        </tr>
        <tr>
            <th>Updated at</th>
            <td><?= Html::encode($ticket->updated_at) ?></td>
        </tr>
    </tbody>
</table>

<h4>Images</h4>

<div id="image-upload-block">
    <input type="file" id="image-upload" accept="image/png,image/jpeg">
    <div id="image-preview" class="d-flex flex-wrap gap-2 mt-2">
        <?php foreach ($ticket->images as $img): ?>
            <div class="img-wrap" data-id="<?= $img->id ?>">
                <img src="<?= $img->getUrl() ?>" width="100">
                <button class="btn btn-sm btn-danger delete-image">×</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<h4>Comments</h4>

<div id="comment-list">
    <?php foreach ($comments as $comment): ?>
        <?= $this->render('_commentItem', ['comment' => $comment]) ?>
    <?php endforeach; ?>
</div>

<div class="comment-form mt-4">
    <?php $form = ActiveForm::begin([
        'id' => 'comment-form',
        'action' => ['tickets/create-comment'],
        'enableAjaxValidation' => false,
    ]); 
    $commentModel = new \app\models\Comment();
    $commentModel->ticket_id = $ticket->id;
    ?>

    <?= $form->field($commentModel, 'text')->textarea(['rows' => 2])->label('New comment') ?>
    <?= Html::activeHiddenInput($commentModel, 'ticket_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$uploadUrl = Url::to(['ticket-image/upload', 'ticket_id' => $ticket->id]);
$deleteUrl = Url::to(['ticket-image/delete']);

$script = <<< JS
$(document).on('beforeSubmit', '#comment-form', function () {
    var form = $(this);

    if (form.find('.has-error').length) return false;

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        success: function (res) {
            if (res.success) {
                $('#comment-list').append(res.html);
                form[0].reset();
            } else {
                alert('Error: ' + JSON.stringify(res.errors));
            }
        },
        error: function () {
            alert('Error in: comment submit.');
        }
    });

    return false;
});

$('#image-upload').on('change', function() {
    var file = this.files[0];
    if (!file) return;

    var formData = new FormData();
    formData.append('image', file);

    $.ajax({
        url: '$uploadUrl',
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(res) {
            if (res.success) {
                var html = '<div class="img-wrap" data-id="' + res.id + '">' +
                           '<img src="' + res.url + '" width="100">' +
                           '<button class="btn btn-sm btn-danger delete-image">×</button>' +
                           '</div>';
                $('#image-preview').append(html);
                $('#image-upload').val('');
            } else {
                alert(res.error);
            }
        },
        error: function() {
            alert('Error in: image upload.');
        }
    });
});

$(document).on('click', '.delete-image', function() {
    var wrap = $(this).closest('.img-wrap');
    var id = wrap.data('id');

    $.ajax({
        url: '$deleteUrl' + '&id=' + id,
        type: 'post',
        success: function(res) {
            if (res.success) {
                wrap.remove();
            } else {
                alert('Error in: image delete.');
            }
        },
        error: function() {
            alert('Error in: image delete.');
        }
    });
});
JS;

$this->registerJs($script);
?>
