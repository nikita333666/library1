<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model app\models\BlogPost */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="blog-post-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'Введите заголовок статьи']) ?>

                    <?= $form->field($model, 'content')->widget(CKEditor::class, [
                        'options' => ['rows' => 15],
                        'preset' => 'full',
                        'clientOptions' => [
                            'height' => 400,
                            'startupMode' => 'source', // Начинаем в режиме HTML
                            'toolbarGroups' => [
                                ['name' => 'mode'], // Кнопка переключения режимов будет первой
                                ['name' => 'document', 'groups' => ['mode', 'document', 'doctools']],
                                ['name' => 'clipboard', 'groups' => ['clipboard', 'undo']],
                                ['name' => 'editing', 'groups' => ['find', 'selection', 'spellchecker']],
                                ['name' => 'forms'],
                                '/',
                                ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
                                ['name' => 'paragraph', 'groups' => ['list', 'indent', 'blocks', 'align', 'bidi']],
                                ['name' => 'links'],
                                ['name' => 'insert'],
                                '/',
                                ['name' => 'styles'],
                                ['name' => 'colors'],
                                ['name' => 'tools'],
                            ],
                            'allowedContent' => true, // Разрешаем любые HTML теги и атрибуты
                            'removeButtons' => '',
                            'fullPage' => false,
                            'removePlugins' => 'elementspath,resize', // Убираем лишние элементы
                            'contentsCss' => [
                                'body { font-family: Arial, sans-serif; font-size: 14px; }',
                            ],
                        ],
                    ])->hint('Используйте кнопку "Source" для переключения между визуальным редактором и HTML-кодом. Вы можете напрямую вставлять HTML-код в режиме Source.') ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'author_firstname')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'Введите имя автора'
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'author_lastname')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'Введите фамилию автора'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Настройки публикации</h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'imageFile')->fileInput(['class' => 'form-control']) ?>

                    <?= $form->field($model, 'img_title')->textInput([
                        'maxlength' => true,
                        'class' => 'form-control',
                        'placeholder' => 'Введите title для изображения'
                    ]) ?>

                    <?= $form->field($model, 'image_alt')->textInput([
                        'maxlength' => true,
                        'class' => 'form-control',
                        'placeholder' => 'Введите alt-текст для изображения'
                    ]) ?>

                    <?php if ($model->image): ?>
                        <div class="current-image">
                            <p class="text-muted mb-2">Текущее изображение:</p>
                            <img src="<?= $model->getImageUrl() ?>" alt="" class="img-fluid rounded">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">SEO настройки</h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'meta_title')->textInput([
                        'maxlength' => true,
                        'class' => 'form-control',
                        'placeholder' => 'Мета-заголовок для SEO'
                    ]) ?>

                    <?= $form->field($model, 'meta_description')->textarea([
                        'rows' => 3,
                        'class' => 'form-control',
                        'placeholder' => 'Мета-описание для SEO'
                    ]) ?>

                    <?= $form->field($model, 'meta_keywords')->textInput([
                        'maxlength' => true,
                        'class' => 'form-control',
                        'placeholder' => 'Ключевые слова через запятую'
                    ]) ?>
                </div>
            </div>

            <div class="form-group mt-3">
                <?= Html::submitButton('Сохранить', [
                    'class' => 'btn btn-success w-100',
                    'style' => 'font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;'
                ]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<style>
.blog-post-form {
    padding: 20px;
    background: #f8f9fa;
}

.card {
    border: none;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #eee;
    padding: 15px 20px;
}

.card-header h5 {
    margin: 0;
    font-size: 1.1em;
    font-weight: 600;
    color: #333;
}

.card-body {
    padding: 20px;
}

.form-control {
    border: 1px solid #ced4da;
    padding: 10px 12px;
    border-radius: 4px;
    font-size: 0.95em;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.current-image {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.current-image img {
    max-width: 100%;
    border-radius: 4px;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
    padding: 12px 20px;
    font-size: 0.9em;
    transition: all 0.2s ease;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-1px);
}

.help-block {
    font-size: 0.85em;
    color: #6c757d;
    margin-top: 5px;
}

.has-error .form-control {
    border-color: #dc3545;
}

.has-error .help-block {
    color: #dc3545;
}
</style>
