<?php
/**
 * Представление для редактирования существующей книги
 * @var $this yii\web\View
 * @var $model app\models\Book
 */

// Подключение хелпера Html для безопасного вывода HTML-кода
use yii\helpers\Html;

// Установка заголовка страницы с названием редактируемой книги
$this->title = 'Редактирование книги: ' . $model->title;
?>

<!-- Основной контейнер страницы редактирования книги -->
<div class="book-update">
    <!-- Блок с заголовком страницы -->
    <div class="header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <!-- Подключение формы для редактирования книги -->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

<!-- Стили для оформления страницы -->
<style>
/* Основной контейнер страницы */
.book-update {
    padding: 20px;
    min-height: calc(100vh - 160px);
}

/* Стили для блока заголовка */
.header {
    margin-bottom: 20px;
}
</style>
