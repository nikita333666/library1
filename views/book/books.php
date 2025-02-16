<?php
// Подключаем необходимые классы Yii2 для работы с формами, HTML и пагинацией
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $books app\models\Book[] Массив объектов книг */
/* @var $categories app\models\Category[] Массив категорий для фильтрации */
/* @var $pagination yii\data\Pagination Объект пагинации */
/* @var $searchQuery string Поисковый запрос пользователя */
/* @var $selectedCategory string Выбранная категория для фильтрации */

// Устанавливаем заголовок страницы и мета-описание
$this->title = 'Библиотека книг';
$this->params['metaDescription'] = 'Исследуйте нашу обширную коллекцию книг. Художественная литература, научные труды, учебники и многое другое.';
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Основной контейнер страницы -->
<div class="books-container">
    <!-- Блок с поисковыми фильтрами -->
    <div class="search-filters">
        <?php // Начало формы поиска и фильтрации
        $form = ActiveForm::begin([
            'action' => ['book/books'],
            'method' => 'get',
            'options' => [
                'class' => 'filter-form',
                'data-pjax' => true // Включаем PJAX для асинхронной загрузки
            ],
        ]); ?>

        <div class="row">
            <!-- Поле поиска по названию или автору -->
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i> <!-- Иконка поиска -->
                        </span>
                        <?= Html::textInput('q', $searchQuery, [
                            'class' => 'form-control',
                            'placeholder' => 'Поиск по названию или автору'
                        ]) ?>
                    </div>
                </div>
            </div>
            <!-- Выпадающий список категорий -->
            <div class="col-md-4">
                <div class="form-group">
                    <?= Html::dropDownList('category_id', 
                        $selectedCategory,
                        ArrayHelper::map($categories, 'id', 'name'), // Преобразуем массив категорий в формат ключ-значение
                        [
                            'class' => 'form-control',
                            'prompt' => 'Все категории'
                        ]
                    ) ?>
                </div>
            </div>
            <!-- Кнопка применения фильтров -->
            <div class="col-md-2">
                <?= Html::submitButton('Применить', ['class' => 'btn btn-primary w-100']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <!-- Сетка с карточками книг -->
    <div class="books-grid">
        <?php if (!empty($books)): // Проверяем, есть ли книги для отображения ?>
            <?php foreach ($books as $book): ?>
                <!-- Карточка отдельной книги -->
                <div class="book-card" data-book-url="<?= Url::to(['book/view', 'id' => $book->id]) ?>">
                    <!-- Блок с обложкой книги -->
                    <div class="book-cover">
                        <?php // Проверяем наличие обложки и файла на диске
                        if ($book->cover_image && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $book->cover_image)): ?>
                            <img src="<?= Yii::getAlias('@web/uploads/covers/') . Html::encode($book->cover_image) ?>" 
                                 alt="<?= Html::encode($book->image_alt ?: 'Обложка книги ' . $book->title) ?>"
                                 title="<?= Html::encode($book->img_title ?: $book->title) ?>"
                                 class="img-fluid">
                        <?php else: // Если обложки нет, показываем заглушку ?>
                            <div class="no-cover">
                                <i class="fas fa-book fa-3x"></i>
                                <span>Нет обложки</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Информация о книге -->
                    <div class="book-info">
                        <h3><?= Html::encode($book->title) ?></h3>
                        <div class="book-details">
                            <!-- Информация об авторе -->
                            <p class="author">
                                <i class="fas fa-user"></i>
                                <?= Html::encode($book->author_firstname . ' ' . $book->author_lastname) ?>
                            </p>
                            <!-- Категория книги (если есть) -->
                            <?php if ($book->category): ?>
                            <p class="category">
                                <i class="fas fa-bookmark"></i>
                                <?= Html::encode($book->category->name) ?>
                            </p>
                            <?php endif; ?>
                            <!-- Краткое описание книги -->
                            <?php if ($book->short_description): ?>
                            <p class="book-short-description">
                                <?= Html::encode($book->short_description) ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        <!-- Кнопки действий с книгой -->
                        <div class="book-actions">
                            <?= Html::a('Подробнее', ['book/view', 'id' => $book->id], [
                                'class' => 'btn btn-primary btn-sm',
                                'onclick' => 'event.stopPropagation();'
                            ]) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: // Если книги не найдены, показываем сообщение ?>
            <div class="no-books-found">
                <i class="fas fa-exclamation-circle"></i>
                <p>Книги не найдены</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Пагинация -->
    <div class="pagination-container">
        <?= LinkPager::widget([
            'pagination' => $pagination,
            'options' => ['class' => 'pagination justify-content-center'],
            'linkContainerOptions' => ['class' => 'page-item'],
            'linkOptions' => ['class' => 'page-link'],
            'disabledListItemSubTagOptions' => ['class' => 'page-link'],
            'maxButtonCount' => 5,
            'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
            'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
        ]) ?>
    </div>
</div>

<!-- CSS стили для оформления страницы -->
<style>
/* Основные стили остаются без изменений */
.books-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Стили для блока фильтров */
.search-filters {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

/* Сетка для отображения книг */
.books-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

/* Стили для карточки книги */
.book-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
    cursor: pointer;
}

/* Эффект при наведении на карточку */
.book-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Стили для блока с обложкой */
.book-cover {
    position: relative;
    width: 100%;
    height: 300px;
    overflow: hidden;
    background: #f8f9fa;
}

/* Стили для изображения обложки */
.book-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Стили для заглушки, если нет обложки */
.no-cover {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: #adb5bd;
}

/* Стили для информации о книге */
.book-info {
    padding: 15px;
}

.book-info h3 {
    margin: 0 0 10px;
    font-size: 1.1em;
    line-height: 1.4;
}

/* Стили для дополнительной информации */
.book-details {
    margin-bottom: 15px;
    font-size: 0.9em;
    color: #6c757d;
}

.book-details p {
    margin-bottom: 5px;
}

.book-details i {
    width: 20px;
    margin-right: 5px;
}

.book-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    align-items: center;
    margin-top: auto;
}

.no-books-found {
    grid-column: 1 / -1;
    text-align: center;
    padding: 50px 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.no-books-found i {
    color: #adb5bd;
    margin-bottom: 15px;
}

.no-books-found p {
    font-size: 1.2em;
    margin: 0 0 5px;
}

.no-books-found small {
    color: #6c757d;
}

.pagination-container {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

.pagination-container {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

.pagination {
    gap: 5px;
}

.pagination .page-link {
    border-radius: 4px;
    color: #2c3e50;
    border: 1px solid #e9ecef;
    padding: 8px 16px;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background-color: #f8f9fa;
    color: #0056b3;
    border-color: #dee2e6;
}

.pagination .active .page-link {
    background-color: #0056b3;
    border-color: #0056b3;
    color: white;
}

.pagination .disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

@media (max-width: 768px) {
    .books-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
    
    .book-cover {
        height: 250px;
    }
}

@media (max-width: 576px) {
    .books-container {
        padding: 10px;
    }
    
    .search-filters {
        padding: 15px;
    }
    
    .books-grid {
        grid-template-columns: 1fr;
    }
}
.short-description {
    font-size: 0.9em;
    color: #666;
    margin: 8px 0;
    line-height: 1.4;
    max-height: 60px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}
</style>

<?php
$js = <<<JS
// Обработчик клика по карточке
$(document).ready(function() {
    $('.book-card').click(function() {
        window.location.href = $(this).data('book-url');
    });
});
JS;
$this->registerJs($js);
?>
