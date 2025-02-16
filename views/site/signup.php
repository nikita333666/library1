<?php
// Подключаем необходимые классы Yii2 для работы с формами и HTML
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Устанавливаем заголовок страницы
$this->title = 'Регистрация';
// Добавляем текущий заголовок в "хлебные крошки"
$this->params['breadcrumbs'][] = $this->title;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- Мета-теги для корректного отображения -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Заголовок страницы -->
    <title>Регистрация</title>
    <!-- Подключаем CSS стили для страницы регистрации -->
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <!-- Основной контейнер страницы регистрации -->
    <div class="site-signup">
        <!-- Контейнер для центрирования формы -->
        <div class="signup-page">
            <!-- Контейнер формы регистрации -->
            <div class="signup-form">
                <!-- Заголовок формы -->
                <h1><?= Html::encode($this->title) ?></h1>

                <!-- Поясняющий текст для пользователя -->
                <p>Пожалуйста, заполните следующие поля для регистрации:</p>

                <?php 
                // Начинаем создание формы регистрации
                $form = ActiveForm::begin([
                    'id' => 'form-signup', // Уникальный идентификатор формы
                    'options' => ['class' => 'signup-form'], // CSS класс для стилизации
                    'enableClientValidation' => true, // Включаем клиентскую валидацию
                ]); ?>

                <?php 
                // Поле для ввода имени пользователя с автофокусом
                echo $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Придумайте имя пользователя']) ?>

                <?php 
                // Поле для ввода email адреса
                echo $form->field($model, 'email')->textInput(['placeholder' => 'Введите email']) ?>

                <?php 
                // Поле для ввода пароля
                echo $form->field($model, 'password')->passwordInput(['placeholder' => 'Придумайте пароль']) ?>

                <?php 
                // Поле для повторного ввода пароля
                echo $form->field($model, 'password_repeat')->passwordInput(['placeholder' => 'Повторите пароль']) ?>

                <!-- Контейнер для кнопки отправки формы -->
                <div class="form-group">
                    <?php 
                    // Кнопка отправки формы регистрации
                    echo Html::submitButton('Зарегистрироваться', [
                        'class' => 'btn btn-primary',
                        'name' => 'signup-button'
                    ]) ?>
                </div>

                <?php 
                // Закрываем форму
                ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</body>
</html>