<?php

use app\models\State;
use yii\helpers\Html;
use yii\bootstrap5\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

?>

<?= Html::button('Create ticket', [
    'class' => 'btn btn-success',
    'id' => 'create-ticket-button',
]) ?>

<?php

Modal::begin([
    'title' => '<h4>Add new ticket</h4>',
    'id' => 'ticket-modal',
    'size' => 'modal-md',
]);

echo "<div id='ticket-modal-content'></div>";

Modal::end();
?>

<h1>Tickets List</h1>

<table class="table" id="tickets-table">
    <thead>
    <tr>
        <th>Ticket ID</th>
        <th>Title</th>
        <th>State</th>
        <th>Created</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($tickets as $ticket): ?>
    <tr class="ticket-row" data-id="<?= $ticket->id ?>" id="ticket-<?= $ticket->id ?>">
        <td><?= Html::encode($ticket->id) ?></td>
        <td><?= Html::encode($ticket->title) ?></td>
        <td>
            <?php
            $states = State::find()->all();
            $items = ArrayHelper::map($states,'id','state');
            ?>
            <?= Html::dropDownList(
                'state',
                $ticket->state,
                $items,
                [
                    'class' => 'form-control ticket-state-dropdown',
                    'data-id' => $ticket->id,
                ]
            ) ?>
        </td>
        <td><?= Html::encode($ticket->created_at) ?></td>
        <td><?= Html::button('Delete', [
            'class' => 'btn btn-danger btn-delete-ticket',
            'data-id' => $ticket->id,
            ]) ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>


<div class="status-message" style="margin-top:10px; color: green;"></div>

<?php
$updateUrl = Url::to(['tickets/update-state']);
$deleteUrl = Url::to(['tickets/delete']);
$createUrl = Url::to(['tickets/create']);

$js = <<<JS
$('#tickets-table').on('change', '.ticket-state-dropdown', function() {
    var taskId = $(this).data('id');
    var newState = $(this).val();

    $.ajax({
        url: '{$updateUrl}',
        type: 'POST',
        data: {
            id: taskId,
            state: newState,
            _csrf: yii.getCsrfToken()
        },
        success: function(response) {
            if (response.success) {
                $('.status-message').text(response.message).css('color', 'green');
            } else {
                $('.status-message').text(response.message).css('color', 'red');
                console.log(response.errors);
            }
        },
        error: function() {
            $('.status-message').text('Request error.').css('color', 'red');
        }
    });
});

$('#tickets-table').on('click', '.btn-delete-ticket', function () {
    if (!confirm('Delete this ticket?')) {
        return;
    }

    var ticketId = $(this).data('id');
    var ticketRow = $('#ticket-' + ticketId);

    $.ajax({
        url: '{$deleteUrl}',
        type: 'POST',
        data: {
            id: ticketId,
            _csrf: yii.getCsrfToken()
        },
        success: function(response) {
            if (response.success) {
                ticketRow.fadeOut(300, function() {
                    $(this).remove();
                });
                $('.status-message').text(response.message).css('color', 'green');
            } else {
                $('.status-message').text(response.message || 'Error, could not delete record.').css('color', 'red');
            }
        },
        error: function() {
            $('.status-message').text('Request error.').css('color', 'red');
        }
    });
});

$('#create-ticket-button').on('click', function() {
    $.get('{$createUrl}', function(data) {
        $('#ticket-modal').modal('show')
            .find('#ticket-modal-content')
            .html(data);
    });
});

$(document).on('beforeSubmit', '#ticket-form', function () {
    var form = $(this);

    $.ajax({
        url: '{$createUrl}',
        type: 'POST',
        data: form.serialize(),
        success: function (response) {
            if (response.success) {
                $('#ticket-modal').modal('hide');
                // $('.status-message').text(response.message).css('color', 'green');
                $('#tickets-table > tbody:first').prepend(response.html); 
                // var tableRef = document.getElementById('tickets-table');
                // tableRef.append(response.html);
                // var newRow = tableRef.insertRow(1);
                // newRow.innerHTML = response.html;

            } else {
                $('.status-message').text(response.message || 'Error, could not add ticket.').css('color', 'red');
            }
        },
        error: function () {
            $('.status-message').text('Request error.').css('color', 'red');
        }
    });
    return false;
});
JS;

$this->registerJs($js);
?>