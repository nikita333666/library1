<?php
/**
 * Представление для редактирования категории
 * @var $this yii\web\View
 * @var $model app\models\Category
 */

// Подключение необходимых компонентов Yii2
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Установка заголовка страницы с названием редактируемой категории
$this->title = 'Редактировать категорию: ' . $model->name;
// Формирование хлебных крошек для навигации
$this->params['breadcrumbs'][] = ['label' => 'Управление категориями', 'url' => ['categories']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view-category', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<!-- Основной контейнер страницы редактирования категории -->
<div class="update-category">
    <!-- Заголовок страницы -->
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Форма для редактирования категории -->
    <div class="category-form">
        <?php 
        // Инициализация формы
        $form = ActiveForm::begin(); ?>

        <!-- Поле для ввода названия категории -->
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <!-- Блок с кнопками действий -->
        <div class="form-group">
            <!-- Кнопка сохранения изменений -->
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <!-- Кнопка отмены с возвратом к списку категорий -->
            <?= Html::a('Отмена', ['categories'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>