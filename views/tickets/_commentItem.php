<?php
use yii\helpers\Html;

/** @var \app\models\Comment $comment */
?>

<div class="comment-item mb-2">
    <div><?= Html::encode($comment->text) ?></div>
    <small class="text-muted"><?= Html::encode($comment->created_at) ?></small>
    <hr>
</div>
