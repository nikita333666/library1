<?php
use yii\helpers\Html;

$this->title = 'Создание блока контента';
$this->params['breadcrumbs'][] = ['label' => 'Управление контентом', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
