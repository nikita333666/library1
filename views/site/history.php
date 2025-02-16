<?php
/**
 * Страница истории просмотров пользователя
 * Отображает список книг, которые пользователь просматривал, с возможностью очистки истории
 *
 * @var yii\web\View $this Текущее представление
 * @var array $viewHistory Массив с историей просмотров книг
 * @var yii\data\Pagination $pages Объект пагинации
 */

// Подключаем необходимые классы Yii2
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\Url;

// Устанавливаем заголовок страницы и подключаем стили для книг
$this->title = 'История просмотров';
$this->registerCssFile('@web/css/books.css', ['depends' => [\app\assets\AppAsset::class]]);
?>

<!-- Основной контейнер страницы истории -->
<div class="history-page">
    <div class="container">
        <!-- Шапка страницы с заголовком и кнопкой очистки -->
        <div class="header-section">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a(
                '<i class="fas fa-trash-alt"></i> Очистить историю',
                ['site/clear-history'],
                [
                    'class' => 'btn btn-outline-danger clear-history-btn',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите очистить всю историю просмотров?',
                        'method' => 'post',
                    ],
                    'style' => 'display: inline-flex; align-items: center; gap: 8px;'
                ]
            ) ?>
        </div>

        <?php 
        // Проверяем наличие истории просмотров
        if (!empty($viewHistory)): ?>
            <!-- Сетка с карточками просмотренных книг -->
            <div class="books-grid">
                <?php 
                // Перебираем все просмотренные книги
                foreach ($viewHistory as $book): ?>
                    <!-- Карточка отдельной книги -->
                    <div class="book-card" style="display: block;" ><a href="<?= Url::to(['book/view', 'id' => $book['id']]) ?>"  style="text-decoration: none; color: inherit;     border: none;  outline: none;display: block; ">
                        <!-- Обложка книги -->
                        <div class="book-cover">
                            <?php 
                            // Проверяем наличие обложки
                            if ($book['cover_image'] && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $book['cover_image'])): ?>
                                <!-- Если обложка есть - показываем её -->
                                <img src="<?= Yii::getAlias('@web') ?>/uploads/covers/<?= Html::encode($book['cover_image']) ?>" 
                                     alt="<?= Html::encode($book['title']) ?>" 
                                     class="cover-image">
                            <?php else: ?>
                                <!-- Если обложки нет - показываем заглушку -->
                                <div class="no-cover">
                                    <span class="no-cover-text">Нет обложки</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Информация о книге -->
                        <div class="book-info">
                            <h3><?= Html::encode($book['title']) ?></h3>
                            <p class="author"><?= Html::encode($book['author_firstname'] . ' ' . $book['author_lastname']) ?></p>
                            <p class="viewed-date">Просмотрено: <?= Yii::$app->formatter->asDatetime($book['viewed_at']) ?></p>
                            <?= Html::a('Подробнее', ['/book/view', 'id' => $book['id']], ['class' => 'btn btn-primary']) ?>
                        </div>
                        </div></a>
                <?php endforeach; ?>
            </div>
            <!-- Виджет пагинации -->
            <div class="pagination-container">
                <?= LinkPager::widget([
                    'pagination' => $pages,
                    'options' => ['class' => 'pagination justify-content-center'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                    'disabledListItemSubTagOptions' => ['class' => 'page-link']
                ]) ?>
            </div>
        <?php else: ?>
            <!-- Сообщение об отсутствии истории -->
            <p class="no-books">История просмотров пуста</p>
        <?php endif; ?>
    </div>
</div>

<!-- Стили для оформления страницы -->
<style>
/* Основной контейнер страницы */
.history-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Стили для шапки страницы */
.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

/* Стили для заголовка */
.header-section h1 {
    margin: 0;
    font-size: 24px;
    color: #333;
}

/* Сетка для отображения книг */
.books-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
    margin-bottom: 30px;
}

/* Стили для карточки книги */
.book-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.2s;
}

/* Эффект при наведении на карточку */
.book-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Контейнер для обложки книги */
.book-cover {
    position: relative;
    width: 100%;
    padding-bottom: 140%;
    background: #f8f9fa;
    overflow: hidden;
}

/* Стили для изображения обложки */
.cover-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Стили для заглушки, если нет обложки */
.no-cover {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f0f0f0;
    color: #666;
}

/* Текст в заглушке */
.no-cover-text {
    font-size: 14px;
}

/* Блок с информацией о книге */
.book-info {
    padding: 15px;
}

/* Стили для заголовка книги с ограничением высоты */
.book-info h3 {
    margin: 0 0 8px;
    font-size: 14px;
    color: #333;
    line-height: 1.3;
    height: 36px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Стили для имени автора с обрезкой длинного текста */
.author {
    color: #666;
    margin: 0 0 8px;
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Стили для даты просмотра */
.viewed-date {
    color: #666;
    margin-bottom: 12px;
    font-size: 12px;
}

/* Общие стили для кнопок */
.btn {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
    text-align: center;
    transition: all 0.2s;
    width: 100%;
    font-size: 13px;
}

/* Стили для основной кнопки */
.btn-primary {
    background: #27ae60;
    color: white;
    border: none;
}

/* Эффект при наведении на основную кнопку */
.btn-primary:hover {
    background: #219a52;
    color: white;
    text-decoration: none;
}

/* Стили для кнопки очистки истории */
.clear-history-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    font-size: 14px;
    color: #dc3545;
    background-color: transparent;
    border: 1px solid #dc3545;
    border-radius: 6px;
    transition: all 0.2s ease;
}

/* Эффект при наведении на кнопку очистки */
.clear-history-btn:hover {
    color: white;
    background-color: #dc3545;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
}

/* Стили для иконки в кнопке очистки */
.clear-history-btn i {
    font-size: 16px;
}

/* Контейнер для пагинации */
.pagination-container {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

/* Стили для списка пагинации */
.pagination {
    display: flex;
    padding-left: 0;
    list-style: none;
    border-radius: 4px;
}

/* Стили для элемента пагинации */
.page-item {
    margin: 0 2px;
}

/* Стили для ссылок пагинации */
.page-item a {
    color: #27ae60;
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 4px;
}

/* Стили для активной страницы */
.page-item.active a {
    background: #27ae60;
    color: white;
}

/* Эффект при наведении на ссылку пагинации */
.page-item a:hover {
    background: #f0f0f0;
}

/* Эффект при наведении на активную страницу */
.page-item.active a:hover {
    background: #219a52;
}

/* Адаптивная верстка для экранов до 1200px */
@media (max-width: 1200px) {
    .books-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Адаптивная верстка для планшетов */
@media (max-width: 768px) {
    .books-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .header-section {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}

/* Адаптивная верстка для мобильных устройств */
@media (max-width: 480px) {
    .books-grid {
        grid-template-columns: 1fr;
    }
}
</style>