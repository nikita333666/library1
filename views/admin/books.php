<?php
/**
 * Представление для отображения списка книг в админ-панели
 * @var $this yii\web\View
 * @var $searchModel app\models\BookSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $categories array
 * @var $books array
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

$this->title = 'Библиотека - Управление книгами';
?>

<!-- Основной контейнер страницы управления книгами -->
<div class="admin-books">
    <!-- Шапка страницы с заголовком и кнопкой добавления -->
    <div class="header">
        <h1>Управление книгами</h1>
        <?= Html::a('<i class="fas fa-plus"></i> Добавить книгу', ['admin/create-book'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php Pjax::begin(); ?>
    <!-- Блок с поисковыми фильтрами -->
    <div class="search-filters">
        <?php $form = ActiveForm::begin([
            'action' => ['admin/books'],
            'method' => 'get',
            'options' => [
                'class' => 'filter-form',
                'data-pjax' => true
            ],
        ]); ?>

        <div class="row">
            <!-- Поле поиска по названию или автору -->
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <?= Html::textInput('BookSearch[search]', $searchModel->search, [
                            'class' => 'form-control',
                            'placeholder' => 'Поиск по названию или автору'
                        ]) ?>
                    </div>
                </div>
            </div>
            <!-- Выпадающий список категорий -->
            <div class="col-md-4">
                <div class="form-group">
                    <?= Html::dropDownList('BookSearch[category_id]', 
                        $searchModel->category_id,
                        ArrayHelper::map($categories, 'id', 'name'),
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

    <!-- Сетка для отображения карточек книг -->
    <div class="books-grid">
        <?php foreach ($dataProvider->getModels() as $book): ?>
            <!-- Карточка книги с дополнительным классом для скрытых книг -->
            <div class="book-card <?= $book->is_hidden ? 'hidden-book' : '' ?>"> <a href="<?= Url::to(['book/view', 'id' => $book->seo_url ?: $book->id]) ?>"  style="text-decoration: none; color: inherit; border: none; outline: none; display: block; ">
                <!-- Контейнер для обложки книги -->
                <div class="book-cover">
                    <?php 
                    // Проверка наличия обложки и файла
                    if ($book->cover_image && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $book->cover_image)): ?>
                        <!-- Отображение обложки книги -->
                        <img src="<?= Yii::getAlias('@web/uploads/covers/') . Html::encode($book->cover_image) ?>" 
                             alt="<?= Html::encode($book->image_alt ?: 'Обложка книги ' . $book->title) ?>" 
                             class="cover-image">
                    <?php else: ?>
                        <!-- Заглушка при отсутствии обложки -->
                        <div class="no-cover">
                            <i class="fas fa-book fa-3x"></i>
                            <span>Нет обложки</span>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Блок информации о книге -->
                <div class="book-info">
                    <!-- Заголовок с названием книги -->
                    <h3><?= Html::encode($book->title) ?></h3>
                    <!-- Информация об авторе -->
                    <div class="info-row">
                        <span class="label">Автор:</span>
                        <span class="value"><?= Html::encode($book->author_firstname . ' ' . $book->author_lastname) ?></span>
                    </div>
                    <!-- Информация о категории -->
                    <div class="info-row">
                        <span class="label">Категория:</span>
                        <span class="value"><?= Html::encode($book->category->name) ?></span>
                    </div>
                </div>
                <!-- Блок с кнопками действий -->
                <div class="book-actions">
                    <!-- Кнопка редактирования книги -->
                    <?= Html::a('<i class="fas fa-edit"></i> Редактировать', 
                        ['admin/update-book', 'id' => $book->id], 
                        ['class' => 'btn btn-primary']
                    ) ?>
                    <!-- Кнопка скрытия/восстановления книги -->
                    <?= Html::a(
                        $book->is_hidden 
                            ? '<i class="fas fa-eye"></i> Восстановить' 
                            : '<i class="fas fa-eye-slash"></i> Скрыть',
                        ['admin/toggle-book-visibility', 'id' => $book->id],
                        [
                            'class' => $book->is_hidden ? 'btn btn-success' : 'btn btn-warning',
                            'data' => [
                                'method' => 'post',
                            ],
                        ]
                    ) ?>
                    <!-- Кнопка удаления книги с подтверждением -->
                    <?= Html::a('<i class="fas fa-trash"></i> Удалить',
                        ['admin/delete-book', 'id' => $book->id],
                        [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                                'method' => 'post',
                            ],
                        ]
                    ) ?>
                </div>
            </div></a>
        <?php endforeach; ?>
    </div>

    <!-- Пагинация -->
    <div class="pagination-container">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'options' => ['class' => 'pagination justify-content-center'],
            'linkContainerOptions' => ['class' => 'page-item'],
            'linkOptions' => ['class' => 'page-link'],
            'disabledListItemSubTagOptions' => ['class' => 'page-link']
        ]) ?>
    </div>
    <?php Pjax::end(); ?>
</div>
<!-- Добавляем элемент для подсказки -->
<div id="tooltip"></div>
<style>
.search-filters {
    background: #f8f9fa;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
#tooltip {
    position: absolute;
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    pointer-events: none;
    z-index: 1000;
    display: none;
}
.filter-form .row {
    margin: 0;
}

.pagination-container {
    margin-top: 20px;
    padding: 20px 0;
}

.books-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px 0;
}

/* Основной контейнер страницы */
.admin-books {
    min-height: calc(100vh - 160px);
    padding: 20px;
}

/* Стили для шапки страницы */
.header {
    margin-bottom: 30px;
}

.header h1 {
    margin-bottom: 20px;
}

/* Стили для карточки книги */
.book-card {
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Стили для скрытых книг */
.hidden-book {
    opacity: 0.7;
    background: #f8f9fa;
}

/* Контейнер для обложки книги */
.book-cover {
    margin-bottom: 15px;
    text-align: center;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

/* Стили для изображения обложки */
.cover-image {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
}

/* Стили для заглушки при отсутствии обложки */
.no-cover {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    color: #6c757d;
}

/* Стили для блока информации о книге */
.book-info {
    margin-bottom: 15px;
}

/* Стили для заголовка книги */
.book-info h3 {
    margin-bottom: 10px;
    font-size: 1.2em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Стили для строк информации */
.info-row {
    margin-bottom: 5px;
}

/* Стили для меток в строках информации */
.info-row .label {
    font-weight: bold;
    margin-right: 5px;
}

/* Стили для блока кнопок действий */
.book-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* Общие стили для кнопок */
.btn {
    padding: 5px 10px;
    font-size: 0.9em;
    white-space: nowrap;
}
</style>
<?php
$js = <<<JS
$(function() {
    var tooltip = $('#tooltip');
    var currentBook = null;
    
    // Используем делегирование через document для работы с Pjax
    $(document).on({
        mousemove: function(e) {
            if (currentBook !== this) {
                currentBook = this;
                tooltip.text($(this).attr('alt'));
            }
            tooltip.css({
                'top': e.pageY + 15,
                'left': e.pageX + 15,
                'display': 'block'
            });
        },
        mouseleave: function() {
            currentBook = null;
            tooltip.css('display', 'none');
        }
    }, '.book-cover img, .book-image');
});
JS;
$this->registerJs($js);
?>