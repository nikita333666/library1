<?php
// Подключаем необходимые классы Yii2
use yii\helpers\Html;

// Устанавливаем заголовок страницы с URL редактируемой страницы
$this->title = 'Редактирование SEO: ' . $model->page_url;
// Добавляем "хлебные крошки" для навигации
$this->params['breadcrumbs'][] = ['label' => 'Управление SEO', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';

// Определяем массив страниц с их человекопонятными названиями
$pages = [
    'site/index' => 'Главная страница',
    'site/about' => 'О нас',
    'book/books' => 'Библиотека книг'
];
?>

<!-- Основной контейнер страницы редактирования -->
<div class="seo-settings-update">
    <!-- Заголовок страницы -->
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Карточка с формой редактирования -->
    <div class="card">
        <!-- Заголовок карточки с названием редактируемой страницы -->
        <div class="card-header">
            <h5 class="mb-0">Редактирование мета-тегов для страницы: <?= Html::encode($pages[$model->page_url]) ?></h5>
        </div>
        <!-- Тело карточки с формой редактирования -->
        <div class="card-body">
            <?= 
            // Подключаем частичное представление с формой редактирования
            $this->render('_form', [
                'model' => $model,
                'availablePages' => $pages,
            ]) 
            ?>
        </div>
    </div>
</div>

<!-- CSS стили для оформления страницы -->
<style>
/* Тень для карточки */
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* Фон заголовка карточки */
.card-header {
    background-color: #f8f9fa;
}

/* Отступ для групп формы */
.form-group {
    margin-bottom: 1rem;
}

/* Стили для сообщений об ошибках */
.help-block {
    color: #dc3545;
    margin-top: 0.25rem;
    font-size: 0.875em;
}
</style>
