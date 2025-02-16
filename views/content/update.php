<?php
use yii\helpers\Html;

$this->title = 'Редактирование контента';
$this->params['breadcrumbs'][] = ['label' => 'Управление контентом', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<div class="content-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
