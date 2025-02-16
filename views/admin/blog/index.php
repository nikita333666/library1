<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BlogPostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Управление статьями';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-post-index">
    <div class="container-fluid">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('Создать статью', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'title',
                [
                    'attribute' => 'author_id',
                    'value' => 'author.username',
                ],
                [
                    'attribute' => 'status',
                    'value' => 'statusText',
                    'filter' => [1 => 'Опубликовано', 0 => 'Черновик'],
                ],
                'views',
                'created_at:datetime',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete} {toggle-status}',
                    'buttons' => [
                        'toggle-status' => function ($url, $model, $key) {
                            $icon = $model->status ? 'eye-slash' : 'eye';
                            $title = $model->status ? 'Скрыть' : 'Показать';
                            return Html::a(
                                '<i class="fas fa-' . $icon . '"></i>',
                                ['toggle-status', 'id' => $model->id],
                                [
                                    'title' => $title,
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-link',
                                ]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>

        <?php Pjax::end(); ?>
    </div>
</div>
