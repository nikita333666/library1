<?php
// Подключение хелпера Html из фреймворка Yii2 для безопасного вывода HTML-кода
use yii\helpers\Html;

// Определение переменной модели книги, которая будет использоваться в представлении
/* @var $model app\models\Book */
?>

<!-- Контейнер для отображения обложки книги -->
<div class="book-cover">
    <?php 
    // Проверяем наличие обложки книги и существование файла на сервере
    if ($model->cover_image && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $model->cover_image)): ?>
        <!-- Отображаем обложку книги с защитой от XSS-атак -->
        <img src="<?= Yii::getAlias('@web/uploads/covers/') . Html::encode($model->cover_image) ?>" 
             alt="<?= Html::encode($model->image_alt ?: 'Обложка книги ' . $model->title) ?>" 
             class="cover-image">
    <?php else: ?>
        <!-- Отображаем заглушку, если обложка отсутствует -->
        <div class="no-cover">
            <!-- Иконка книги из библиотеки Font Awesome -->
            <i class="fas fa-book fa-3x"></i>
            <span>Нет обложки</span>
        </div>
    <?php endif; ?>
</div>

<!-- Контейнер для отображения основной информации о книге -->
<div class="book-info">
    <!-- Заголовок с названием книги (защищен от XSS) -->
    <h3><?= Html::encode($model->title) ?></h3>
    <!-- Строка с информацией об авторе -->
    <div class="info-row">
        <span class="label">Автор:</span>
        <!-- Вывод полного имени автора с защитой от XSS -->
        <span class="value"><?= Html::encode($model->author_firstname . ' ' . $model->author_lastname) ?></span>
    </div>
    <!-- Строка с информацией о категории книги -->
    <div class="info-row">
        <span class="label">Категория:</span>
        <!-- Вывод названия категории с защитой от XSS -->
        <span class="value"><?= Html::encode($model->category->name) ?></span>
    </div>
    <!-- Строка с кратким описанием книги -->
    <div class="info-row">
        <span class="label">Краткое описание:</span>
        <!-- Вывод краткого описания с защитой от XSS -->
        <span class="value description"><?= Html::encode($model->short_description) ?></span>
    </div>
</div>

<!-- Контейнер для кнопок управления книгой -->
<div class="book-actions">
    <!-- Кнопка для перехода к редактированию книги -->
    <?= Html::a('<i class="fas fa-edit"></i> Редактировать', 
        ['admin/update-book', 'id' => $model->id], 
        ['class' => 'btn btn-primary']
    ) ?>
    <!-- Кнопка для удаления книги с подтверждением действия -->
    <?= Html::a('<i class="fas fa-trash"></i> Удалить',
        ['admin/delete-book', 'id' => $model->id],
        [
            'class' => 'btn-delete',
            // Настройки для диалога подтверждения удаления
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                'method' => 'post',
            ],
        ]
    ) ?>
</div>
