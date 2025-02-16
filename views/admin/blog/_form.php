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
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'content')->widget(CKEditor::class, [
                'options' => ['rows' => 6],
                'preset' => 'full'
            ]) ?>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Настройки публикации</h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'status')->dropDownList([
                        1 => 'Опубликовано',
                        0 => 'Черновик'
                    ]) ?>

                    <?= $form->field($model, 'imageFile')->fileInput(['class' => 'form-control']) ?>

                    <?php if ($model->image): ?>
                        <div class="current-image mb-3">
                            <p>Текущее изображение:</p>
                            <img src="<?= $model->getImageUrl() ?>" alt="" class="img-fluid">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">SEO настройки</h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'meta_title')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'meta_description')->textarea(['rows' => 2]) ?>
                    <?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="form-group mt-3">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-block']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
