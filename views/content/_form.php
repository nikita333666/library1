<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PageContent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="content-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'page_url')->textInput(['maxlength' => true, 'readonly' => !$model->isNewRecord]) ?>

    <?= $form->field($model, 'block_identifier')->textInput(['maxlength' => true, 'readonly' => !$model->isNewRecord]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<div class="alert alert-info">
    <h4>Доступные идентификаторы блоков для главной страницы:</h4>
    <ul>
        <li><code>welcome_title</code> - Заголовок приветствия</li>
        <li><code>welcome_subtitle</code> - Подзаголовок приветствия</li>
        <li><code>feature_1_title</code> - Заголовок первого преимущества</li>
        <li><code>feature_1_text</code> - Текст первого преимущества</li>
        <li><code>feature_2_title</code> - Заголовок второго преимущества</li>
        <li><code>feature_2_text</code> - Текст второго преимущества</li>
        <li><code>feature_3_title</code> - Заголовок третьего преимущества</li>
        <li><code>feature_3_text</code> - Текст третьего преимущества</li>
    </ul>
</div>
