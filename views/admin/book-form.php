<?php
// Подключение необходимых компонентов Yii2 для работы с формами и манипуляции данными
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
?>

<!-- Основной контейнер формы книги -->
<div class="book-form">
    <?php 
    // Инициализация формы с поддержкой загрузки файлов
    $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <!-- Секция основной информации о книге -->
    <div class="form-section">
        <h3>Основная информация</h3>
        <!-- Поле для ввода названия книги -->
        <?= $form->field($model, 'title')->textInput(['maxlength' => true])->label('Название книги') ?>
        
        <!-- Блок полей для ввода информации об авторе -->
        <div class="author-fields">
            <!-- Поле для имени автора -->
            <?= $form->field($model, 'author_firstname')->textInput(['maxlength' => true])->label('Имя автора') ?>
            <!-- Поле для фамилии автора -->
            <?= $form->field($model, 'author_lastname')->textInput(['maxlength' => true])->label('Фамилия автора') ?>
        </div>
        
        <!-- Поля для описаний книги -->
        <?= $form->field($model, 'short_description')->textarea(['rows' => 3])->label('Краткое описание') ?>
        <?= $form->field($model, 'description')->textarea(['rows' => 6])->label('Полное описание') ?>
        
        <!-- Выпадающий список категорий -->
        <?= $form->field($model, 'category_id')->dropDownList(
            ArrayHelper::map($categories, 'id', 'name'),
            ['prompt' => 'Выберите категорию']
        )->label('Категория') ?>
    </div>

    <!-- Секция для загрузки файлов -->
    <div class="form-section">
        <h3>Файлы</h3>
        
        <!-- Контейнер для организации загрузки файлов -->
        <div class="file-upload-section">
            <!-- Блок для загрузки обложки книги -->
            <div class="file-upload-block">
                <h4>Обложка книги</h4>
                <!-- Поле для загрузки изображения обложки -->
                <?= $form->field($model, 'coverFile')->fileInput([
                    'accept' => 'image/*',
                    'class' => 'form-control file-input'
                ])->label(false) ?>
                
                <?php 
                // Отображение текущей обложки для существующей книги
                if (!$model->isNewRecord && $model->cover_image): ?>
                    <div class="current-file">
                        <p>Текущая обложка:</p>
                        <!-- Отображение текущего изображения обложки -->
                        <img src="/library/web/uploads/covers/<?= Html::encode($model->cover_image) ?>" 
                             alt="<?= Html::encode($model->title) ?>">
                    </div>
                <?php endif; ?>
            </div>

            <!-- Блок для загрузки PDF файла книги -->
            <div class="file-upload-block">
                <h4>PDF файл книги</h4>
                <!-- Поле для загрузки PDF файла -->
                <?= $form->field($model, 'pdfFile')->fileInput([
                    'accept' => 'application/pdf',
                    'class' => 'form-control file-input'
                ])->label(false) ?>
                
                <?php 
                // Отображение информации о текущем PDF файле
                if (!$model->isNewRecord && $model->pdf_file): ?>
                    <div class="current-file">
                        <p>Текущий файл: <?= Html::encode($model->pdf_file) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Блок кнопок действий -->
    <div class="form-actions">
        <!-- Кнопка сохранения с разным текстом для новой/существующей книги -->
        <?= Html::submitButton($model->isNewRecord ? 'Создать книгу' : 'Сохранить изменения', [
            'class' => 'btn btn-success'
        ]) ?>
        <!-- Кнопка отмены с возвратом к списку книг -->
        <?= Html::a('Отмена', ['books'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- Стили для оформления формы -->
<style>
/* Основной контейнер формы */
.book-form {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Стили для секций формы */
.form-section {
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 6px;
}

/* Стили для заголовков */
.form-section h3 {
    margin: 0 0 20px 0;
    color: #333;
    font-size: 1.4em;
}

.form-section h4 {
    margin: 0 0 10px 0;
    color: #555;
    font-size: 1.1em;
}

/* Сетка для полей автора */
.author-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

/* Сетка для секции загрузки файлов */
.file-upload-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

/* Стили для блоков загрузки файлов */
.file-upload-block {
    padding: 15px;
    background: white;
    border-radius: 4px;
    border: 1px solid #ddd;
}

/* Стили для отображения текущих файлов */
.current-file {
    margin-top: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

/* Стили для предпросмотра изображения */
.current-file img {
    max-width: 200px;
    height: auto;
    display: block;
    margin-top: 10px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Стили для текста в блоке текущего файла */
.current-file p {
    margin: 0;
    color: #666;
    font-size: 0.9em;
}

/* Стили для блока кнопок */
.form-actions {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    text-align: right;
}

/* Общие стили для кнопок */
.btn {
    padding: 8px 20px;
    border-radius: 4px;
    font-size: 1em;
    cursor: pointer;
    transition: all 0.2s;
}

/* Стили для кнопки успешного действия */
.btn-success {
    background: #28a745;
    color: white;
    border: none;
}

.btn-success:hover {
    background: #218838;
}

/* Стили для кнопки отмены */
.btn-secondary {
    background: #6c757d;
    color: white;
    border: none;
    margin-left: 10px;
}

.btn-secondary:hover {
    background: #5a6268;
}

/* Стили для элементов формы */
.form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1em;
}

.form-control:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

/* Медиа-запрос для адаптивности на мобильных устройствах */
@media (max-width: 768px) {
    .file-upload-section,
    .author-fields {
        grid-template-columns: 1fr;
    }
    
    .book-form {
        padding: 15px;
    }
    
    .form-section {
        padding: 15px;
    }
}
</style>
