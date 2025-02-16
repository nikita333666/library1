<?php
// Подключаем необходимые классы Yii2 для работы с HTML и формами
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Устанавливаем заголовок страницы
$this->title = 'Вход';
// Добавляем текущий заголовок в "хлебные крошки"
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Основной контейнер страницы входа -->
<div class="site-login">
    <!-- Контейнер для центрирования формы -->
    <div class="login-page">
        <!-- Контейнер самой формы входа -->
        <div class="login-form">
            <!-- Выводим заголовок страницы, используя HTML-хелпер для безопасного вывода -->
            <h1><?= Html::encode($this->title) ?></h1>

            <!-- Поясняющий текст для пользователя -->
            <p>Пожалуйста, заполните следующие поля для входа:</p>

            <?php 
            // Начинаем создание формы с помощью ActiveForm
            $form = ActiveForm::begin([
                'id' => 'login-form', // Уникальный идентификатор формы
                'options' => ['class' => 'login-form'], // CSS класс для стилизации
                'enableClientValidation' => true, // Включаем клиентскую валидацию
            ]); ?>

            <?php 
            // Создаем поле ввода для имени пользователя
            echo $form->field($model, 'username', [
                'options' => ['class' => 'form-group']
            ])->textInput(['autofocus' => true, 'placeholder' => 'Введите имя пользователя']) ?>

            <?php 
            // Создаем поле ввода для пароля
            echo $form->field($model, 'password', [
                'options' => ['class' => 'form-group']
            ])->passwordInput(['placeholder' => 'Введите пароль']) ?>
            
            <!-- Контейнер для кнопки отправки формы -->
            <div class="form-group">
                <?php 
                // Создаем кнопку отправки формы
                echo Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>

            <?php 
            // Закрываем форму
            ActiveForm::end(); ?>
        </div>
    </div>
</div>
