<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\BlogPost */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Управление статьями', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-post-view">
    <div class="container-fluid">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить эту статью?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a(
                $model->status ? 'Скрыть' : 'Показать',
                ['toggle-status', 'id' => $model->id],
                ['class' => 'btn btn-info']
            ) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'title',
                'content:raw',
                [
                    'attribute' => 'image',
                    'format' => 'html',
                    'value' => function($model) {
                        return $model->image ? Html::img($model->getImageUrl(), ['style' => 'max-width:200px;']) : null;
                    },
                ],
                [
                    'attribute' => 'author_id',
                    'value' => $model->author->username,
                ],
                'created_at:datetime',
                'updated_at:datetime',
                [
                    'attribute' => 'status',
                    'value' => $model->statusText,
                ],
                'views',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'slug',
            ],
        ]) ?>

        <div class="comments-section mt-4">
            <h3>Комментарии</h3>
            <?php foreach ($model->comments as $comment): ?>
                <div class="comment-item card mb-3">
                    <div class="card-body">
                        <h6><?= Html::encode($comment->user->username) ?></h6>
                        <p><?= Html::encode($comment->content) ?></p>
                        <small class="text-muted">
                            <?= Yii::$app->formatter->asDatetime($comment->created_at) ?>
                        </small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
