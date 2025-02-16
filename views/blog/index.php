<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BlogPostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Блог';
$this->registerMetaTag(['name' => 'description', 'content' => 'Блог библиотеки - интересные статьи о книгах, авторах и литературе']);
$this->registerMetaTag(['name' => 'keywords', 'content' => 'блог, библиотека, книги, статьи, литература']);

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-post-index">
    <div class="container">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()): ?>
            <p>
                <?= Html::a('Создать статью', ['create'], ['class' => 'btn btn-success']) ?>
            </p>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="search-form">
                    <?php $form = \yii\widgets\ActiveForm::begin([
                        'action' => ['index'],
                        'method' => 'get',
                        'options' => ['class' => 'form-inline']
                    ]); ?>

                    <div class="input-group">
                        <?= $form->field($searchModel, 'title', [
                            'template' => "{input}",
                            'options' => ['class' => 'mb-0']
                        ])->textInput([
                            'class' => 'form-control',
                            'placeholder' => 'Поиск по названию статьи...'
                        ]) ?>
                        <div class="input-group-append">
                            <?= Html::submitButton('<i class="fas fa-search"></i>', [
                                'class' => 'btn btn-primary'
                            ]) ?>
                        </div>
                    </div>

                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <style>
        .search-form {
            margin-bottom: 20px;
        }
        .search-form .input-group {
            width: 100%;
        }
        .search-form .form-group {
            margin-bottom: 0;
            width: 100%;
        }
        .search-form .input-group-append {
            margin-left: -1px;
        }
        .search-form .btn-primary {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        .search-form .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        </style>

        <?php Pjax::begin(); ?>
        <div class="row">
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemOptions' => ['class' => 'col-lg-6 col-md-6 mb-4'],
                'itemView' => '_post',
                'layout' => "<div class='row'>{items}</div>\n<div class='pagination-wrapper'>{pager}</div>",
                'pager' => [
                    'class' => LinkPager::class,
                    'options' => ['class' => 'pagination'],
                    'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
                    'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
                ],
            ]) ?>
        </div>
        <?php Pjax::end(); ?>
    </div>
</div>

<style>
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}
.pagination {
    display: flex;
    padding-left: 0;
    list-style: none;
    border-radius: 0.25rem;
}
.pagination > li {
    margin: 0 2px;
}
.pagination > li > a {
    position: relative;
    display: block;
    padding: 0.5rem 0.75rem;
    margin-left: -1px;
    line-height: 1.25;
    color: #007bff;
    background-color: #fff;
    border: 1px solid #dee2e6;
    text-decoration: none;
}
.pagination > li.active > a {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}
.pagination > li > a:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}
</style>
