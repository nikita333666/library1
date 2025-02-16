<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model app\models\BlogPost */
/* @var $comments app\models\BlogComment[] */

// Устанавливаем мета-теги
$this->title = $model->meta_title ?: $model->title;
$this->registerMetaTag(['name' => 'description', 'content' => $model->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $model->meta_keywords]);

$this->params['breadcrumbs'][] = ['label' => 'Блог', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="blog-post-view">
    <div class="container">
        <article class="blog-post">
            <?php if ($model->image): ?>
                <div class="post-image">
                    <img src="<?= $model->getImageUrl() ?>" 
                         alt="<?= Html::encode($model->image_alt) ?>"
                         title="<?= Html::encode($model->img_title) ?>"
                         class="img-fluid rounded shadow-sm">
                </div>
            <?php endif; ?>

            <div class="post-meta">
                <span class="post-date">
                    <i class="fas fa-calendar-alt text-primary"></i> 
                    <?= Yii::$app->formatter->asDate($model->created_at, 'long') ?>
                </span>
                <span class="post-author">
                    <i class="fas fa-user text-primary"></i>
                    <?= Html::encode($model->author_firstname . ' ' . $model->author_lastname) ?>
                </span>
            </div>

            <h1 class="post-title"><?= Html::encode($this->title) ?></h1>

            <div class="blog-content">
                <?= Yii::$app->htmlSanitizer->sanitize($model->content) ?>
            </div>

            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()): ?>
                <div class="admin-actions mt-4">
                    <div class="btn-group">
                        <?= Html::a('<i class="fas fa-edit"></i> Редактировать', ['update', 'id' => $model->id], [
                            'class' => 'btn btn-outline-primary btn-sm',
                        ]) ?>
                        <?= Html::a('<i class="fas fa-trash"></i> Удалить', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-outline-danger btn-sm',
                            'data' => [
                                'confirm' => 'Вы уверены, что хотите удалить эту статью?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </div>
                </div>
            <?php endif; ?>
        </article>

        <div class="comments-section mt-5">
            <h3 class="mb-4"><i class="fas fa-comments text-primary"></i> Комментарии</h3>
            
            <?php if (!Yii::$app->user->isGuest): ?>
                <div class="comment-form mb-4">
                    <?php $form = ActiveForm::begin([
                        'action' => ['/blog/add-comment', 'id' => $model->id],
                        'options' => ['class' => 'shadow-sm p-3 bg-white rounded']
                    ]); ?>
                    
                    <div class="form-group">
                        <textarea name="comment" class="form-control" rows="3" 
                                placeholder="Напишите ваш комментарий..." required></textarea>
                    </div>

                    <div class="form-group text-right mb-0">
                        <?= Html::submitButton('<i class="fas fa-paper-plane"></i> Отправить', [
                            'class' => 'btn btn-primary'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            <?php else: ?>
                <div class="alert alert-light text-center">
                    <i class="fas fa-lock"></i> Чтобы оставить комментарий, пожалуйста, <?= Html::a('войдите', ['/site/login'], ['class' => 'alert-link']) ?>
                </div>
            <?php endif; ?>

            <div class="comments-list" data-post-id="<?= $model->id ?>">
                <?php if (!empty($comments)): ?>
                    <?php 
                    // Получаем первые 4 комментария
                    $initialComments = array_slice($comments, 0, 4);
                    foreach ($initialComments as $comment): ?>
                        <div class="comment-item mb-3 p-3 bg-white rounded shadow-sm">
                            <div class="comment-header d-flex justify-content-between align-items-center">
                                <div class="comment-info">
                                    <span class="comment-author font-weight-bold">
                                        <i class="fas fa-user-circle text-primary"></i> 
                                        <?= Html::encode($comment->user->username) ?>
                                    </span>
                                    <span class="comment-date text-muted ml-3">
                                        <i class="fas fa-clock"></i>
                                        <?= Yii::$app->formatter->asRelativeTime($comment->created_at) ?>
                                    </span>
                                </div>
                                <?php if (!Yii::$app->user->isGuest && (Yii::$app->user->identity->isAdmin() || $comment->user_id === Yii::$app->user->id)): ?>
                                    <div class="comment-actions">
                                        <?= Html::a('<i class="fas fa-trash"></i>', ['delete-comment', 'id' => $comment->id], [
                                            'class' => 'btn btn-link text-danger',
                                            'title' => 'Удалить комментарий',
                                            'data' => [
                                                'confirm' => 'Вы уверены, что хотите удалить этот комментарий?',
                                                'method' => 'post',
                                            ],
                                        ]) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="comment-content mt-2">
                                <?= Html::encode($comment->content) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <p>Пока нет комментариев. Будьте первым!</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (count($comments) > 4): ?>
                <div class="load-more-container text-center mt-4">
                    <button class="btn btn-outline-primary load-more-comments" 
                            data-offset="4" 
                            data-post-id="<?= $model->id ?>">
                        <i class="fas fa-comments"></i> Показать ещё комментарии
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.blog-post-view {
    padding: 40px 0;
    background-color: #f8f9fa;
}

.blog-post {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.post-content {
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: pre-line;
    max-width: 100%;
    line-height: 1.6;
    font-size: 16px;
}

.post-content p {
    margin-bottom: 1.5em;
}

.post-image {
    margin-bottom: 20px;
}

.post-image img {
    max-width: 100%;
    height: auto;
}

.post-meta {
    margin-bottom: 20px;
    color: #666;
}

.post-meta span {
    margin-right: 20px;
}

.post-title {
    margin-bottom: 30px;
    color: #333;
    font-size: 2.5em;
    font-weight: bold;
}

.comment-form textarea {
    border: 1px solid #e9ecef;
    transition: border-color 0.2s ease;
}

.comment-form textarea:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.comment-item {
    transition: transform 0.2s ease;
}

.comment-item:hover {
    transform: translateY(-2px);
}

.comment-author {
    color: #2c3e50;
}

.comment-date {
    font-size: 0.85rem;
}

.comment-content {
    color: #2c3e50;
    line-height: 1.5;
}

.load-more-comments {
    padding: 8px 20px;
    font-size: 0.9rem;
}

.load-more-comments i {
    margin-right: 5px;
}

.alert-link {
    text-decoration: none;
}

.alert-link:hover {
    text-decoration: underline;
}

.blog-content {
    word-wrap: break-word;      /* Перенос длинных слов */
    overflow-wrap: break-word;  /* Современное свойство для переноса */
    word-break: break-word;     /* Разрешаем перенос внутри слов */
    -ms-word-break: break-all;  /* Поддержка для IE */
    -ms-hyphens: auto;          /* Автоматические переносы для IE */
    -moz-hyphens: auto;         /* Автоматические переносы для Firefox */
    -webkit-hyphens: auto;      /* Автоматические переносы для Chrome/Safari */
    hyphens: auto;              /* Автоматические переносы */
    max-width: 100%;            /* Ограничиваем максимальную ширину */
    font-size: 1.1em;
    line-height: 1.6;
    color: #333;
}

.blog-content p, 
.blog-content div, 
.blog-content span {
    max-width: 100%;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.blog-content img {
    max-width: 100%;
    height: auto;
    margin: 1.5em 0;
    border-radius: 4px;
}

.blog-content blockquote {
    margin: 1.5em 0;
    padding: 1em 1.5em;
    border-left: 4px solid #007bff;
    background-color: #f8f9fa;
    font-style: italic;
}

.blog-content pre {
    background-color: #f8f9fa;
    padding: 1em;
    border-radius: 4px;
    overflow-x: auto;
    margin: 1.5em 0;
}

.blog-content code {
    background-color: #f8f9fa;
    padding: 0.2em 0.4em;
    border-radius: 3px;
    font-family: monospace;
}

.blog-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5em 0;
}

.blog-content th,
.blog-content td {
    padding: 0.75em;
    border: 1px solid #dee2e6;
}

.blog-content th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.blog-content a {
    color: #007bff;
    text-decoration: none;
}

.blog-content a:hover {
    text-decoration: underline;
}

/* Поддержка пользовательских стилей */
.blog-content [style] {
    max-width: 100%;
}

.blog-content span[style*="color"],
.blog-content p[style*="color"],
.blog-content div[style*="color"] {
    display: inline-block;
}
</style>

<?php
$script = <<<JS
$(document).ready(function() {
    $('.load-more-comments').click(function() {
        var btn = $(this);
        var offset = btn.data('offset');
        var postId = btn.data('post-id');
        var container = $('.comments-list');
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Загрузка...');
        
        $.get('/library/web/blog/load-more-comments', {
            post_id: postId,
            offset: offset
        }, function(response) {
            container.append(response.html);
            btn.data('offset', response.nextOffset);
            
            if (!response.hasMore) {
                btn.parent().fadeOut();
            }
            
            btn.prop('disabled', false).html('<i class="fas fa-comments"></i> Показать ещё комментарии');
        });
    });
});
JS;

$this->registerJs($script);
?>
