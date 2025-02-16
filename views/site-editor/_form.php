<?php
// Подключаем необходимые классы Yii2
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Определяем переменные, используемые в представлении
/* @var $this yii\web\View */
/* @var $model app\models\SiteSettings */
/* @var $form yii\widgets\ActiveForm */
?>

<!-- Основной контейнер формы настроек сайта -->
<div class="site-settings-form">
    <?php 
    // Начинаем создание формы с помощью ActiveForm
    $form = ActiveForm::begin(); 
    ?>

    <!-- Поле для ввода заголовка сайта -->
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <!-- Поле для ввода ключевых слов сайта -->
    <?= $form->field($model, 'keywords')->textarea(['rows' => 4]) ?>

    <!-- Поле для ввода описания сайта -->
    <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>

    <!-- Группа кнопок формы -->
    <div class="form-group">
        <!-- Кнопка сохранения формы -->
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php 
    // Завершаем форму
    ActiveForm::end(); 
    ?>
</div>
