<?php
// Подключаем необходимые классы Yii2
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Определяем переменные, используемые в представлении
/* @var $this yii\web\View */
/* @var $model app\models\SeoSettings */
/* @var $form yii\widgets\ActiveForm */
?>

<!-- Основной контейнер формы SEO настроек -->
<div class="seo-settings-form">
    <?php 
    // Начинаем создание формы с помощью ActiveForm
    $form = ActiveForm::begin(); 
    ?>

    <?php 
    // Если создается новая запись, показываем выпадающий список страниц
    if ($model->isNewRecord): ?>
        <?= $form->field($model, 'page_url')->dropDownList($availablePages, [
            'prompt' => 'Выберите страницу...',
        ]) ?>
    <?php 
    // Если редактируется существующая запись, показываем название страницы
    else: ?>
        <!-- Информационный блок с названием текущей страницы -->
        <div class="alert alert-info">
            <strong>Страница:</strong> <?= $availablePages[$model->page_url] ?>
        </div>
        <!-- Скрытое поле с URL страницы -->
        <?= $form->field($model, 'page_url')->hiddenInput()->label(false) ?>
    <?php endif; ?>

    <!-- Карточка с основными SEO настройками -->
    <div class="card mb-3">
        <!-- Заголовок карточки -->
        <div class="card-header">
            <h5>SEO настройки</h5>
        </div>
        <!-- Тело карточки с полями ввода -->
        <div class="card-body">
            <!-- Поле для ввода Title с подсказками -->
            <?= $form->field($model, 'title')->textInput(['maxlength' => true])->hint('

                   
            ') ?>

            <!-- Поле для ввода Description с подсказками -->
            <?= $form->field($model, 'description')->textarea(['rows' => 6])->hint('

            ') ?>

            <!-- Поле для ввода Keywords с подсказками -->
            <?= $form->field($model, 'keywords')->textarea(['rows' => 4])->hint('

            ') ?>
        </div>
    </div>

    <!-- Группа кнопок формы -->
    <div class="form-group">
        <!-- Кнопка сохранения формы -->
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <!-- Кнопка отмены с возвратом к списку -->
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php 
    // Завершаем форму
    ActiveForm::end(); 
    ?>
</div>

<!-- CSS стили для оформления формы -->
<style>
/* Тень для карточек */
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
/* Фон заголовка карточки */
.card-header {
    background-color: #f8f9fa;
}
/* Отступ для групп формы */
.form-group {
    margin-bottom: 1rem;
}
/* Стили для сообщений об ошибках */
.help-block {
    color: #dc3545;
    margin-top: 0.25rem;
    font-size: 0.875em;
}
</style>

<?php
// Регистрируем JavaScript код для интерактивной валидации полей
$this->registerJs("
    // Функция обновления счетчиков символов
    function updateCounter(input, counter) {
        // Получаем текущую длину текста
        var length = input.val().length;
        // Обновляем текст счетчика
        counter.text(length + ' символов');
        
        // Удаляем все классы цветового оформления
        counter.removeClass('text-success text-warning text-danger');

        // Проверяем поле title
        if (input.is('[name=\"SeoSettings[title]\"]')) {
            // Добавляем соответствующий класс в зависимости от длины
            if (length >= 50 && length <= 60) {
                counter.addClass('text-success'); // Оптимальная длина
            } else if (length < 50) {
                counter.addClass('text-warning'); // Слишком короткий
            } else {
                counter.addClass('text-danger');  // Слишком длинный
            }
        } 
        // Проверяем поле description
        else if (input.is('[name=\"SeoSettings[description]\"]')) {
            // Добавляем соответствующий класс в зависимости от длины
            if (length >= 150 && length <= 160) {
                counter.addClass('text-success'); // Оптимальная длина
            } else if (length < 150) {
                counter.addClass('text-warning'); // Слишком короткий
            } else {
                counter.addClass('text-danger');  // Слишком длинный
            }
        }
    }

   
");
?>
