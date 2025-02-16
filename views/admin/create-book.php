<?php
/**
 * Представление для создания новой книги
 * @var $this yii\web\View
 * @var $model app\models\Book
 */

// Подключение хелпера Html для безопасного вывода HTML-кода
use yii\helpers\Html;

// Установка заголовка страницы
$this->title = 'Добавление новой книги';
?>

<!-- Основной контейнер страницы создания книги -->
<div class="book-create">
    <!-- Блок с заголовком страницы -->
    <div class="header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <!-- Подключение формы для создания/редактирования книги -->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

<!-- Стили для оформления страницы -->
<style>
/* Основной контейнер страницы */
.book-create {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Стили для блока заголовка */
.header {
    margin-bottom: 30px;
}

/* Стили для заголовка страницы */
.header h1 {
    margin: 0;
    color: #333;
}
</style>
