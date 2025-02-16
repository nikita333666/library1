<?php
/**
 * Представление для обновления существующего пользователя
 * @var $this yii\web\View
 * @var $model app\models\User
 */

// Подключение необходимых компонентов Yii2
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Установка заголовка страницы с именем редактируемого пользователя
$this->title = 'Редактировать пользователя: ' . $model->username;
// Формирование хлебных крошек для навигации
$this->params['breadcrumbs'][] = ['label' => 'Управление пользователями', 'url' => ['users']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<!-- Основной контейнер страницы редактирования пользователя -->
<div class="update-user">
    <!-- Заголовок страницы -->
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Форма для редактирования пользователя -->
    <div class="user-form">
        <?php 
        // Инициализация формы
        $form = ActiveForm::begin(); ?>

        <!-- Поле для редактирования имени пользователя -->
        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        <!-- Поле для редактирования email -->
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?php 
        // Проверка прав доступа: только суперадмин может менять роли других пользователей
        // (кроме других суперадминов)
        if (Yii::$app->user->identity->is_admin == 2 && $model->is_admin != 2): ?>
            <!-- Выпадающий список для выбора роли пользователя -->
            <?= $form->field($model, 'is_admin')->dropDownList([
                0 => 'Пользователь',
                1 => 'Администратор'
            ]) ?>
        <?php endif; ?>

        <!-- Блок с кнопкой сохранения -->
        <div class="form-group">
            <!-- Кнопка сохранения изменений -->
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>