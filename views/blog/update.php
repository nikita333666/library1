<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BlogPost */

$this->title = 'Редактировать статью: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Блог', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="blog-post-update">
    <div class="container">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
