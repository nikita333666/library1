<?php
/**
 * Страница избранных книг пользователя
 * Отображает список книг, добавленных пользователем в избранное
 *
 * @var yii\web\View $this Текущее представление
 * @var array $books Массив с избранными книгами
 * @var yii\data\Pagination $pagination Объект пагинации
 */

// Подключаем необходимые классы Yii2
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\Url;

// Устанавливаем заголовок страницы и подключаем CSS файл для стилизации книг
$this->title = 'Избранные книги';
$this->registerCssFile('@web/css/books.css', ['depends' => [\app\assets\AppAsset::class]]);
?>

<!-- Основной контейнер страницы избранного -->
<div class="favorites-page">
    <div class="container">
        <!-- Секция с заголовком страницы -->
        <div class="header-section">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>

        <?php 
        // Проверяем, есть ли книги в избранном
        if (!empty($books)): ?>
            <!-- Сетка с карточками книг -->
            <div class="books-grid">
                <?php 
                // Перебираем все книги и выводим их карточки
                foreach ($books as $book): ?>
                    <!-- Карточка отдельной книги -->
                    <div class="book-card" style="display: block;" ><a href="<?= Url::to(['book/view', 'id' => $book['id']]) ?>"  style="text-decoration: none; color: inherit;     border: none;  outline: none;display: block; ">
                        <!-- Обложка книги -->
                        <div class="book-cover">
                            <?php 
                            // Проверяем наличие обложки книги
                            if ($book['cover_image'] && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $book['cover_image'])): ?>
                                <!-- Если обложка есть - выводим её -->
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
                            <p class="added-date">Добавлено: <?= Yii::$app->formatter->asDatetime($book['created_at']) ?></p>
                            <?= Html::a('Подробнее', ['/book/view', 'id' => $book['id']], ['class' => 'btn btn-primary']) ?>
                        </div>
                            </div></a>
                <?php endforeach; ?>
            </div>

            <!-- Виджет пагинации для навигации по страницам -->
            <div class="pagination-container">
                <?= LinkPager::widget([
                    'pagination' => $pagination,
                    'options' => ['class' => 'pagination justify-content-center'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                    'disabledListItemSubTagOptions' => ['class' => 'page-link']
                ]) ?>
            </div>
        <?php else: ?>
            <!-- Сообщение, если список избранного пуст -->
            <p class="no-books">У вас пока нет избранных книг</p>
        <?php endif; ?>
    </div>
</div>

<!-- Стили для оформления страницы -->
<style>
/* Основной контейнер страницы */
.favorites-page {
    padding: 20px 0;
}

/* Стили для шапки с заголовком */
.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 0 15px;
}

/* Контейнер с ограничением по ширине */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Сетка для отображения книг */
.books-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

/* Стили для карточки книги */
.book-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Эффект при наведении на карточку */
.book-card:hover {
    transform: translateY(-5px);
}

/* Контейнер для обложки книги */
.book-cover {
    position: relative;
    padding-top: 150%;
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
    background-color: #f0f0f0;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Текст в заглушке */
.no-cover-text {
    font-size: 14px;
    color: #666;
}

/* Блок с информацией о книге */
.book-info {
    padding: 15px;
}

/* Заголовок книги */
.book-info h3 {
    margin: 0 0 10px;
    font-size: 16px;
    line-height: 1.3;
}

/* Стили для имени автора */
.author {
    color: #666;
    margin-bottom: 10px;
    font-size: 14px;
}

/* Стили для даты добавления */
.added-date {
    color: #666;
    margin-bottom: 15px;
    font-size: 14px;
}

/* Стили для кнопки */
.btn-primary {
    display: inline-block;
    padding: 8px 16px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s;
}

/* Эффект при наведении на кнопку */
.btn-primary:hover {
    background-color: #0056b3;
    color: white;
}

/* Стили для сообщения об отсутствии книг */
.no-books {
    text-align: center;
    color: #666;
    margin-top: 30px;
    font-size: 16px;
}

/* Контейнер для пагинации */
.pagination-container {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

/* Медиа-запрос для адаптивности на мобильных устройствах */
@media (max-width: 768px) {
    .books-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
    
    .book-info h3 {
        font-size: 14px;
    }
    
    .author {
        font-size: 12px;
    }
}
</style>