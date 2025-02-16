<?php
/**
 * Представление для создания новой категории
 * @var $this yii\web\View
 * @var $model app\models\Category
 */

// Подключение необходимых компонентов Yii2
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Установка заголовка страницы
$this->title = 'Создать категорию';
// Формирование хлебных крошек для навигации
$this->params['breadcrumbs'][] = ['label' => 'Управление категориями', 'url' => ['categories']];
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Основной контейнер страницы создания категории -->
<div class="create-category">
    <!-- Заголовок страницы -->
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Форма для создания категории -->
    <div class="category-form">
        <?php 
        // Инициализация формы
        $form = ActiveForm::begin(); ?>

        <!-- Поле для ввода названия категории -->
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <!-- Блок с кнопкой отправки формы -->
        <div class="form-group">
            <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>