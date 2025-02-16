<?php
// Подключение необходимых компонентов Yii2 для работы с формами и данными
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
// Подключение модели категорий для формирования выпадающего списка
use app\models\Category;
?>

<!-- Основной контейнер формы для работы с книгой -->
<div class="book-form">
    <?php 
    // Инициализация формы с поддержкой загрузки файлов (multipart/form-data)
    $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <!-- Разделение формы на две колонки для лучшей организации -->
    <div class="row">
        <!-- Левая колонка с основной информацией о книге -->
        <div class="col-md-6">
            <?= 
            // Поле для ввода названия книги с ограничением длины
            $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            <!-- Поля для ввода информации об авторе -->
            <?= $form->field($model, 'author_firstname')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'author_lastname')->textInput(['maxlength' => true]) ?>
            
            <!-- Выпадающий список для выбора категории книги -->
            <?= $form->field($model, 'category_id')->dropDownList(
                // Формирование массива для выпадающего списка: id => name
                ArrayHelper::map(Category::find()->all(), 'id', 'name'),
                ['prompt' => 'Выберите категорию']
            ) ?>
        </div>
        
        <!-- Правая колонка для загрузки файлов -->
        <div class="col-md-6">
            <!-- Секция загрузки обложки книги -->
            <?= $form->field($model, 'coverFile')->fileInput() ?>
            <?php 
            // Отображение текущей обложки книги, если она существует
            if ($model->cover_image): ?>
                <div class="current-cover">
                    <p>Текущая обложка:</p>
                    <!-- Отображение текущего изображения обложки -->
                    <img src="<?= Yii::getAlias('@web/uploads/covers/') . $model->cover_image ?>" 
                         alt="<?= Html::encode($model->image_alt) ?>" 
                         title="<?= Html::encode($model->img_title) ?>"
                         style="max-width: 200px;">
                </div>
            <?php endif; ?>
            
            <!-- Секция загрузки PDF файла книги -->
            <?= $form->field($model, 'pdfFile')->fileInput() ?>
            <?php 
            // Отображение информации о текущем PDF файле, если он существует
            if ($model->pdf_file): ?>
                <div class="current-pdf">
                    <p>Текущий PDF: <?= Html::encode($model->pdf_file) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Секция для ввода описаний книги -->
    <div class="row">
        <div class="col-md-12">
            <!-- Поле для краткого описания книги -->
            <?= $form->field($model, 'short_description')->textarea(['rows' => 3]) ?>
            <!-- Поле для полного описания книги -->
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <!-- Секция для SEO-оптимизации -->
    <div class="row">
        <div class="col-md-12">
            <h4>META-теги и SEO</h4>
            <div class="meta-section">
                <!-- Поле для ключевых слов -->
                <?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => true]) ?>
                <!-- Поле для мета-описания -->
                <?= $form->field($model, 'meta_description')->textarea(['rows' => 3]) ?>
                <!-- Поле для альтернативного текста изображения -->
                <?= $form->field($model, 'image_alt')->textInput(['maxlength' => true]) ?>
                <!-- Поле для title текста изображения -->
                <?= $form->field($model, 'img_title')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'seo_url')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>

    <!-- Кнопка отправки формы -->
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- Стили для оформления формы -->
<style>
/* Основной контейнер формы */
.book-form {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Стили для секции мета-тегов */
.meta-section {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    margin-top: 10px;
}

/* Стили для блоков с текущими файлами */
.current-cover,
.current-pdf {
    margin: 10px 0;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

/* Отступ для группы кнопок */
.form-group {
    margin-top: 20px;
}
</style>
