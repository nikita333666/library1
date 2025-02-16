<?php
// Подключаем необходимые классы Yii2
use yii\helpers\Html;
use yii\grid\GridView;

// Определяем переменные, используемые в представлении
/* @var $this yii\web\View */
/* @var $seoSettings array */
/* @var $pages array */

// Устанавливаем заголовок страницы
$this->title = 'Управление SEO';
// Добавляем заголовок в "хлебные крошки"
$this->params['breadcrumbs'][] = $this->title;

// Определяем массив страниц с их человекопонятными названиями
$pages = [
    'site/index' => 'Главная страница',
    'site/about' => 'О нас',
    'book/books' => 'Библиотека книг'
];
?>

<!-- Основной контейнер страницы -->
<div class="seo-settings-index">
    <!-- Заголовок страницы -->
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Контейнер с таблицей, адаптивный для мобильных устройств -->
    <div class="table-responsive">
        <!-- Таблица со списком SEO настроек -->
        <table class="table table-striped">
            <!-- Заголовок таблицы -->
            <thead>
                <tr>
                    <th>Страница</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Keywords</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <!-- Тело таблицы -->
            <tbody>
                <?php 
                // Перебираем все SEO настройки
                foreach ($seoSettings as $setting): ?>
                    <tr>
                        <!-- Название страницы (если есть в массиве $pages, иначе исходный URL) -->
                        <td><?= Html::encode($pages[$setting->page_url] ?? $setting->page_url) ?></td>
                        <!-- Мета-заголовок страницы -->
                        <td><?= Html::encode($setting->title) ?></td>
                        <!-- Мета-описание страницы -->
                        <td><?= Html::encode($setting->description) ?></td>
                        <!-- Ключевые слова страницы -->
                        <td><?= Html::encode($setting->keywords) ?></td>
                        <!-- Кнопки действий -->
                        <td>
                            <?= Html::a('<i class="fas fa-edit"></i>', 
                                ['update', 'id' => $setting->id], 
                                ['class' => 'btn btn-primary btn-sm']
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- CSS стили для оформления таблицы -->
<style>
/* Основные стили таблицы */
.table {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Стили для заголовков таблицы */
.table th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

/* Стили для ячеек таблицы */
.table td, .table th {
    padding: 12px;
    vertical-align: middle;
}

/* Стили для маленьких кнопок */
.btn-sm {
    padding: 5px 10px;
}

/* Стили для ячеек с длинным текстом */
.table td {
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
