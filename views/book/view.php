<?php
/**
 * Представление для просмотра детальной информации о книге
 * @var $this yii\web\View Объект представления
 * @var $model app\models\Book Модель книги
 * @var $comments array Массив комментариев к книге
 */

// Подключаем необходимые классы Yii2
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Comment;
use app\models\Favorite;

// Устанавливаем мета-теги для SEO
$this->title = $model->title;
if ($model->meta_description) {
    $this->registerMetaTag(['name' => 'description', 'content' => $model->meta_description]);
}
if ($model->meta_keywords) {
    $this->registerMetaTag(['name' => 'keywords', 'content' => $model->meta_keywords]);
}

// Настраиваем хлебные крошки
$this->params['breadcrumbs'][] = ['label' => 'Библиотека', 'url' => ['books']];
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Основной контейнер страницы -->
<div class="book-view">
    <!-- Блок с основной информацией о книге -->
    <div class="book-container">
        <!-- Блок с обложкой книги -->
        <div class="book-cover">
            <?php // Проверяем наличие обложки и файла
            if ($model->cover_image && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $model->cover_image)): ?>
                <img src="<?= Yii::getAlias('@web/uploads/covers/') . Html::encode($model->cover_image) ?>" 
                     alt="<?= Html::encode($model->image_alt) ?>" 
                     title="<?= Html::encode($model->img_title ?: $model->title) ?>"
                     class="img-fluid">
            <?php else: // Если обложки нет, показываем заглушку ?>
                <div class="no-cover">
                    <i class="fas fa-book fa-3x"></i>
                    <span>Нет обложки</span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Блок с деталями книги -->
        <div class="book-details">
            <h1><?= Html::encode($model->title) ?></h1>
            
            <!-- Информация об авторе -->
            <div class="author-info">
                <h3>Автор</h3>
                <p><?= Html::encode($model->author_firstname . ' ' . $model->author_lastname) ?></p>
            </div>
            
            <!-- Категория книги -->
            <div class="category-info">
                <h3>Категория</h3>
                <p><?= Html::encode($model->category->name) ?></p>
            </div>

            <!-- Кнопка для чтения PDF, если файл существует -->
            <?php if ($model->pdf_file && file_exists(Yii::getAlias('@webroot/uploads/pdfs/') . $model->pdf_file)): ?>
            <div class="read-button">
                <a href="<?= Yii::getAlias('@web/uploads/pdfs/') . Html::encode($model->pdf_file) ?>" 
                   class="btn btn-primary btn-lg" 
                   target="_blank">
                    <i class="fas fa-book-reader"></i> Начать чтение
                </a>
            </div>
            <?php endif; ?>
            
            <!-- Кнопка добавления в избранное для авторизованных пользователей -->
            <?php if (!Yii::$app->user->isGuest): ?>
                <?php
                // Проверяем, добавлена ли книга в избранное
                $isFavorite = Favorite::find()
                    ->where(['user_id' => Yii::$app->user->id, 'book_id' => $model->id])
                    ->exists();
                ?>
                <div class="book-actions mt-4">
                    <button type="button"
                       class="btn btn-favorite<?= $isFavorite ? ' active' : '' ?>"
                       data-url="<?= Url::to(['/book/toggle-favorite']) ?>"
                       data-id="<?= $model->id ?>">
                        <i class="fas fa-heart<?= $isFavorite ? '' : '-o' ?>"></i>
                        <span><?= $isFavorite ? 'Убрать из избранного' : 'Добавить в избранное' ?></span>
                    </button>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Блок с описаниями книги -->
    <div class="book-descriptions">
        <?php if ($model->short_description): ?>
        <div class="short-description">
            <h3>Краткое описание</h3>
            <p><?= Html::encode($model->short_description) ?></p>
        </div>
        <?php endif; ?>
        
        <?php if ($model->description): ?>
        <div class="full-description">
            <h3>Полное описание</h3>
            <p><?= Html::encode($model->description) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Секция комментариев -->
    <div class="comments-section">
        <h2>Комментарии</h2>
        
        <!-- Форма добавления комментария для авторизованных пользователей -->
        <?php if (!Yii::$app->user->isGuest): ?>
            <div class="comment-form">
                <?php $form = ActiveForm::begin(['action' => ['/book/add-comment', 'id' => $model->id]]); ?>
                    <div class="form-group">
                        <textarea name="comment" class="form-control" rows="3" placeholder="Оставьте свой комментарий..." required></textarea>
                    </div>
                    <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        <?php endif; ?>

        <!-- Список комментариев -->
        <div class="comments-list" data-book-id="<?= $model->id ?>">
            <?php 
            // Получаем первые 4 комментария
            $initialComments = array_slice($comments, 0, 4);
            if (!empty($initialComments)): 
                foreach ($initialComments as $comment):
                    echo $this->render('_comment', ['comment' => $comment]);
                endforeach;
            else: ?>
                <p class="no-comments">Пока нет комментариев. Будьте первым!</p>
            <?php endif; ?>
        </div>

        <?php if (count($comments) > 4): ?>
            <div class="load-more-container text-center mt-4">
                <button class="btn btn-outline-primary load-more-comments" 
                        data-offset="4" 
                        data-book-id="<?= $model->id ?>">
                    <i class="fas fa-comments"></i> Показать ещё комментарии
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- CSS стили для оформления страницы -->
<style>
/* Основной контейнер страницы */
.book-view {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Сетка для основной информации о книге */
.book-container {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 30px;
    margin-bottom: 40px;
}

/* Стили для блока с обложкой */
.book-cover {
    max-width: 300px;
    margin: 0 auto;
}

/* Стили для изображения обложки */
.book-cover img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Стили для заглушки, если нет обложки */
.no-cover {
    width: 100%;
    height: 400px;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    color: #666;
}

/* Стили для текста заглушки */
.no-cover-text {
    font-size: 1.5em;
}

/* Стили для заголовка книги */
.book-details h1 {
    margin: 0 0 20px 0;
    color: #333;
    font-size: 2.5em;
}

/* Стили для блоков с информацией */
.author-info, .category-info {
    margin-bottom: 20px;
}

/* Стили для подзаголовков */
.author-info h3, .category-info h3 {
    margin: 0 0 5px 0;
    color: #666;
    font-size: 1.2em;
}

/* Стили для кнопки добавления в избранное */
.btn-favorite {
    background: #fff;
    border: 2px solid #dc3545;
    color: #dc3545;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
}

/* Стили для кнопки добавления в избранное при наведении */
.btn-favorite:hover, .btn-favorite.active {
    background: #dc3545;
    color: #fff;
}

/* Стили для описаний книги */
.book-descriptions {
    margin-bottom: 40px;
}

/* Стили для краткого описания */
.short-description, .full-description {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

/* Стили для секции комментариев */
.comments-section {
    margin-top: 40px;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Стили для формы добавления комментария */
.comment-form {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
}

/* Стили для списка комментариев */
.comments-list {
    margin-top: 30px;
}

/* Стили для комментария */
.comment {
    padding: 15px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s;
}

.comment:last-child {
    border-bottom: none;
}

.comment:hover {
    background-color: #f8f9fa;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.comment-author {
    font-weight: bold;
    color: #2c3e50;
}

.comment-meta {
    display: flex;
    align-items: center;
    gap: 10px;
}

.comment-date {
    color: #6c757d;
    font-size: 0.9em;
}

.comment-text {
    color: #2c3e50;
    line-height: 1.5;
}

.delete-comment {
    color: #dc3545;
    text-decoration: none;
}

.delete-comment:hover {
    color: #c82333;
}

.load-more-comments {
    transition: all 0.3s;
}

.load-more-comments:hover {
    transform: translateY(-2px);
}

.load-more-container {
    opacity: 1;
    transition: opacity 0.3s;
}

.load-more-container.hidden {
    opacity: 0;
    pointer-events: none;
}

/* Адаптивные стили для мобильных устройств */
@media (max-width: 768px) {
    .book-container {
        grid-template-columns: 1fr;
    }
    
    .book-cover {
        max-width: 300px;
        margin: 0 auto;
    }
}
</style>

<?php
$script = <<<JS
$(document).ready(function() {
    // Обработчик клика по кнопке "Показать ещё"
    $('.load-more-comments').click(function() {
        var btn = $(this);
        var offset = btn.data('offset');
        var bookId = btn.data('book-id');
        var container = $('.comments-list');
        
        // Отключаем кнопку на время загрузки
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Загрузка...');
        
        // Загружаем дополнительные комментарии
        $.get('/library/web/book/load-comments', {
            id: bookId,
            offset: offset
        })
        .done(function(response) {
            if (response.success) {
                // Добавляем новые комментарии
                container.append(response.html);
                
                // Обновляем смещение для следующей загрузки
                btn.data('offset', response.nextOffset);
                
                // Если больше нет комментариев, скрываем кнопку
                if (!response.hasMore) {
                    btn.parent().fadeOut();
                }
            }
        })
        .fail(function() {
            alert('Произошла ошибка при загрузке комментариев');
        })
        .always(function() {
            // Включаем кнопку обратно
            btn.prop('disabled', false).html('<i class="fas fa-comments"></i> Показать ещё комментарии');
        });
    });
});
JS;
$this->registerJs($script);
?>
<?php
$script = <<<JS
$(document).ready(function() {
    // Обработчик клика по кнопке "Добавить в избранное"
    $('.btn-favorite').click(function() {
        var btn = $(this);
        var url = btn.data('url');
        var bookId = btn.data('id');
        
        // Отправляем AJAX-запрос на сервер
        $.ajax({
            url: url,
            type: 'POST',
            data: { id: bookId },
            success: function(response) {
                if (response.success) {
                    // Обновляем состояние кнопки
                    if (response.is_favorite) {
                        btn.addClass('active');
                        btn.find('i').removeClass('fa-heart-o').addClass('fa-heart');
                        btn.find('span').text('Убрать из избранного');
                    } else {
                        btn.removeClass('active');
                        btn.find('i').removeClass('fa-heart').addClass('fa-heart-o');
                        btn.find('span').text('Добавить в избранное');
                    }
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Произошла ошибка при обработке запроса');
            }
        });
    });
});
JS;
$this->registerJs($script);
?>
