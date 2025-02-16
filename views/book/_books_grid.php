<?php
/**
 * Шаблон для отображения сетки книг в виде карточек
 * @var $books array Массив моделей книг из базы данных
 * @var $pagination yii\data\Pagination Объект пагинации для постраничной навигации
 */

// Подключаем необходимые классы Yii2
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\Url;
?>

<!-- Контейнер для сетки книг -->
<div class="books-grid">
    <?php // Перебираем все книги в массиве
    foreach ($books as $index => $book): ?>
        <!-- Начало карточки отдельной книги -->
        <div class="col-md-4 mb-4">
            <div class="book-card" data-book-url="<?= Url::to(['book/view', 'id' => $book->seo_url ?: $book->id]) ?>">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <button type="button" class="favorite-btn" 
                            data-book-id="<?= $book->id ?>"
                            onclick="event.stopPropagation(); toggleFavorite(this)">
                        <i class="fas fa-heart"></i>
                    </button>
                <?php endif; ?>
                
                <!-- Блок с изображением обложки книги -->
                <?php if ($book->cover_image): // Проверяем наличие обложки ?>
                    <img src="<?= Yii::getAlias('@web/uploads/covers/') . $book->cover_image ?>" 
                         alt="<?= Html::encode($book->image_alt ?: $book->title) ?>" 
                         title="<?= Html::encode($book->img_title ?: $book->title) ?>"
                         class="book-cover">
                <?php else: // Если обложки нет, показываем заглушку ?>
                    <div class="no-cover">
                        <i class="fas fa-book"></i>
                        <span>Нет обложки</span>
                    </div>
                <?php endif; ?>

                <!-- Основная информация о книге -->
                <h4><?= Html::encode($book->title) ?></h4> <!-- Безопасный вывод названия -->
                <p class="text-muted"><?= Html::encode($book->author) ?></p> <!-- Имя автора -->
                <?php if ($book->short_description): ?>
                    <p class="book-short-description"><?= Html::encode($book->short_description) ?></p>
                <?php endif; ?>
                <p class="book-description"><?= Html::encode($book->description) ?></p> <!-- Описание книги -->
                
                <!-- Кнопки действий с книгой -->
                <div class="book-actions">
                    <a href="<?= Url::to(['book/view', 'id' => $book->seo_url ?: $book->id]) ?>" 
                       class="btn btn-primary"
                       onclick="event.stopPropagation();">Подробнее</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Блок пагинации для навигации по страницам -->
<div class="pagination-container d-flex justify-content-center">
    <?= LinkPager::widget([
        'pagination' => $pagination,
        'options' => ['class' => 'pagination'],
        'linkOptions' => ['class' => 'page-link'],
        'linkContainerOptions' => ['class' => 'page-item'],
        'disabledListItemSubTagOptions' => ['class' => 'page-link']
    ]) ?>
</div>

<style>
.book-card {
    position: relative;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    background: #fff;
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    min-height: 500px; /* Добавляем минимальную высоту для карточки */
}

.book-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.book-cover {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 4px;
    margin-bottom: 15px;
}

.no-cover {
    width: 100%;
    height: 300px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    border-radius: 4px;
    margin-bottom: 15px;
}

.favorite-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 1;
    transition: transform 0.2s;
}

.favorite-btn:hover {
    transform: scale(1.1);
}

.favorite-btn i {
    color: #dc3545;
    font-size: 1.2em;
}

.favorite-btn i.active {
    color: #dc3545;
}

.book-description {
    margin: 10px 0;
    color: #666;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.book-short-description {
    font-size: 0.9em;
    color: #666;
    margin: 10px 0;
    line-height: 1.4;
    max-height: 60px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}

.book-actions {
    margin-top: 15px;
}

.book-actions .btn {
    width: 100%;
}
</style>

<?php
$js = <<<JS
function toggleFavorite(btn) {
    var bookId = $(btn).data('book-id');
    $.post('/book/toggle-favorite', {book_id: bookId}, function(response) {
        if (response.success) {
            $(btn).find('i').toggleClass('active');
        } else {
            alert('Произошла ошибка. Пожалуйста, попробуйте позже.');
        }
    }).fail(function() {
        alert('Произошла ошибка. Пожалуйста, попробуйте позже.');
    });
}

// Добавляем обработчик клика на карточку
$(document).ready(function() {
    $('.book-card').click(function() {
        window.location.href = $(this).data('book-url');
    });
});
JS;
// Регистрируем JavaScript код на странице
$this->registerJs($js);
?>
