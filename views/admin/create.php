<?php
/**
 * Представление для создания новой книги
 * @var $this yii\web\View
 * @var $model app\models\Book
 */

// Подключение хелпера Html для безопасного вывода HTML-кода
use yii\helpers\Html;

// Установка заголовка страницы
$this->title = 'Добавить книгу';
// Формирование хлебных крошек для навигации
$this->params['breadcrumbs'][] = ['label' => 'Управление книгами', 'url' => ['books']];
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Основной контейнер страницы создания книги -->
<div class="create-book">
    <!-- Заголовок страницы -->
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Подключение формы для создания книги -->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>