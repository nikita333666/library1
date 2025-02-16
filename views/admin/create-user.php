<?php
/**
 * Представление для создания нового пользователя
 * @var $this yii\web\View
 * @var $model app\models\User
 */

// Подключение необходимых компонентов Yii2
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Установка заголовка страницы
$this->title = 'Создать пользователя';
// Формирование хлебных крошек для навигации
$this->params['breadcrumbs'][] = ['label' => 'Управление пользователями', 'url' => ['users']];
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Основной контейнер страницы создания пользователя -->
<div class="create-user">
    <!-- Заголовок страницы -->
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Форма для создания пользователя -->
    <div class="user-form">
        <?php 
        // Инициализация формы
        $form = ActiveForm::begin(); ?>

        <!-- Поле для ввода имени пользователя -->
        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        <!-- Поле для ввода email -->
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        
        <?php 
        // Проверка, является ли текущий пользователь владельцем системы
        if (Yii::$app->user->identity->isOwner()): ?>
            <!-- Выпадающий список для выбора роли (только для владельца системы) -->
            <?= $form->field($model, 'is_admin')->dropDownList($model->getRolesList()) ?>
        <?php endif; ?>

        <!-- Поля для ввода пароля -->
        <?= $form->field($model, 'new_password')->passwordInput() ?>
        <?= $form->field($model, 'new_password_repeat')->passwordInput() ?>

        <!-- Блок с кнопками действий -->
        <div class="form-group">
            <!-- Кнопка создания пользователя -->
            <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
            <!-- Кнопка отмены с возвратом к списку пользователей -->
            <?= Html::a('Отмена', ['users'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<!-- Стили для оформления страницы -->
<style>
/* Стили для формы пользователя */
.user-form {
    max-width: 600px;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* Отступ для группы кнопок */
.form-group {
    margin-top: 20px;
}

/* Отступ между кнопками */
.btn {
    margin-right: 10px;
}
</style>
