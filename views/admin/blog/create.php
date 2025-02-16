<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BlogPost */

$this->title = 'Создать статью';
$this->params['breadcrumbs'][] = ['label' => 'Управление статьями', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-post-create">
    <div class="container-fluid">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
