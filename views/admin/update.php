<?php
/**
 * Представление для обновления существующей книги
 * @var $this yii\web\View
 * @var $model app\models\Book
 */

// Подключение хелпера Html для безопасного вывода HTML-кода
use yii\helpers\Html;

// Установка заголовка страницы с названием редактируемой книги
$this->title = 'Редактировать книгу: ' . $model->title;
// Формирование хлебных крошек для навигации
$this->params['breadcrumbs'][] = ['label' => 'Управление книгами', 'url' => ['books']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<!-- Основной контейнер страницы редактирования книги -->
<div class="update-book">
    <!-- Заголовок страницы -->
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Подключение формы для редактирования книги -->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>