<?php

/** 
 * Страница контактной формы
 * @var yii\web\View $this Текущее представление
 * @var yii\bootstrap5\ActiveForm $form Объект формы Bootstrap 5
 * @var app\models\ContactForm $model Модель формы обратной связи
 */

// Подключаем необходимые классы Yii2
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\captcha\Captcha;

// Устанавливаем заголовок страницы и добавляем в "хлебные крошки"
$this->title = 'Contact';
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- Основной контейнер страницы контактов -->
<div class="site-contact">
    <!-- Заголовок страницы -->
    <h1><?= Html::encode($this->title) ?></h1>

    <?php 
    // Проверяем, была ли форма успешно отправлена
    if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <!-- Сообщение об успешной отправке -->
        <div class="alert alert-success">
            Thank you for contacting us. We will respond to you as soon as possible.
        </div>

        <!-- Информация для разработчиков о работе с почтой -->
        <p>
            Note that if you turn on the Yii debugger, you should be able
            to view the mail message on the mail panel of the debugger.
            <?php 
            // Проверяем, используется ли файловый транспорт для почты (режим разработки)
            if (Yii::$app->mailer->useFileTransport): ?>
                Because the application is in development mode, the email is not sent but saved as
                a file under <code><?= Yii::getAlias(Yii::$app->mailer->fileTransportPath) ?></code>.
                Please configure the <code>useFileTransport</code> property of the <code>mail</code>
                application component to be false to enable email sending.
            <?php endif; ?>
        </p>

    <?php else: ?>

        <!-- Описание формы обратной связи -->
        <p>
            If you have business inquiries or other questions, please fill out the following form to contact us.
            Thank you.
        </p>

        <!-- Контейнер с формой -->
        <div class="row">
            <div class="col-lg-5">
                <?php 
                // Начало формы обратной связи
                $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                    <!-- Поле для ввода имени с автофокусом -->
                    <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

                    <!-- Поле для ввода email -->
                    <?= $form->field($model, 'email') ?>

                    <!-- Поле для ввода темы сообщения -->
                    <?= $form->field($model, 'subject') ?>

                    <!-- Поле для ввода текста сообщения -->
                    <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

                    <!-- Поле с CAPTCHA для защиты от спама -->
                    <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                        'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                    ]) ?>

                    <!-- Кнопка отправки формы -->
                    <div class="form-group">
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>

    <?php endif; ?>
</div>
