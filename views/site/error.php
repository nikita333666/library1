<?php

/**
 * Страница отображения ошибок
 * @var yii\web\View $this Текущее представление
 * @var string $name Название ошибки
 * @var string $message Сообщение об ошибке
 * @var Exception $exception Объект исключения
 */

// Подключаем хелпер для работы с HTML
use yii\helpers\Html;

// Устанавливаем заголовок страницы равным названию ошибки
$this->title = $name;
?>
<!-- Основной контейнер страницы ошибки -->
<div class="site-error">

    <!-- Заголовок с названием ошибки -->
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Блок с сообщением об ошибке -->
    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <!-- Пояснительный текст для пользователя -->
    <p>
        The above error occurred while the Web server was processing your request.
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>

</div>
