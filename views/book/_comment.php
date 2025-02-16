<?php
use yii\helpers\Html;
?>

<div class="comment">
    <!-- Заголовок комментария с информацией о пользователе -->
    <div class="comment-header">
        <span class="comment-author"><?= Html::encode($comment->user->username) ?></span>
        <div class="comment-meta">
            <span class="comment-date"><?= Yii::$app->formatter->asDatetime($comment->created_at) ?></span>
            <!-- Кнопка удаления для админа или автора комментария -->
            <?php if (!Yii::$app->user->isGuest && (Yii::$app->user->identity->is_admin || Yii::$app->user->id === $comment->user_id)): ?>
                <?= Html::a('<i class="fas fa-trash"></i>', ['/book/delete-comment', 'id' => $comment->id], [
                    'class' => 'delete-comment',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить этот комментарий?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>
    <!-- Текст комментария -->
    <div class="comment-text">
        <?= Html::encode($comment->text) ?>
    </div>
</div>
