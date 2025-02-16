<?php
// Подключаем необходимые классы Yii2
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\Modal;
use yii\bootstrap5\ActiveForm;

// Устанавливаем заголовок страницы
$this->title = 'Профиль пользователя';
// Подключаем CSS файл для стилизации профиля
$this->registerCssFile('@web/css/profile.css');
?>

<!-- Основной контейнер профиля -->
<div class="profile-container">
    <div class="profile-layout">
        <!-- Левая колонка с информацией о пользователе -->
        <div class="user-info-column">
            <div class="user-info">
                <!-- Отображаем имя пользователя -->
                <h1><?= Html::encode(Yii::$app->user->identity->username) ?></h1>
                <!-- Отображаем email пользователя -->
                <p class="email"><?= Html::encode(Yii::$app->user->identity->email) ?></p>
                <?php
                    // Определяем роль пользователя на основе значения is_admin
                    $role = '';
                    switch (Yii::$app->user->identity->is_admin) {
                        case 2:
                            $role = 'Владелец';
                            break;
                        case 1:
                            $role = 'Администратор';
                            break;
                        default:
                            $role = 'Пользователь';
                    }
                ?>
                <!-- Отображаем роль пользователя -->
                <p class="role">Роль: <?= Html::encode($role) ?></p>
                
                <!-- Кнопка для открытия модального окна смены пароля -->
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    Изменить пароль
                </button>
                
                <!-- Кнопка выхода из системы -->
                <?= Html::a('Выйти', ['/site/logout'], ['class' => 'logout-button', 'data-method' => 'post']) ?>
            </div>
        </div>

        <!-- Правая колонка с избранным и историей -->
        <div class="content-column">
            <!-- Секция избранных книг -->
            <div class="favorites-section">
                <div class="section-header">
                    <h2>Избранные книги</h2>
                    <!-- Ссылка на полный список избранного -->
                    <?= Html::a('Показать все', ['site/favorites'], ['class' => 'show-all']) ?>
                </div>
                <!-- Сетка с книгами -->
                <div class="books-grid">
                    <?php if (!empty($favoriteBooks)): ?>
                        <?php // Показываем только первые 3 книги из избранного
                        foreach (array_slice($favoriteBooks, 0, 3) as $book): ?>
                            <!-- Карточка книги -->
                            <div class="book-card" data-book-url="<?= Url::to(['book/view', 'id' => $book->id]) ?>">
                                <div class="book-cover">
                                    <?php if ($book->cover_image && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $book->cover_image)): ?>
                                        <img src="<?= Yii::getAlias('@web/uploads/covers/') . Html::encode($book->cover_image) ?>" 
                                             alt="<?= Html::encode($book->title) ?>" 
                                             class="img-fluid">
                                    <?php else: ?>
                                        <div class="no-cover">
                                            <i class="fas fa-book fa-3x"></i>
                                            <span>Нет обложки</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="book-info">
                                    <h3><?= Html::encode($book->title) ?></h3>
                                    <div class="book-details">
                                        <p class="author">
                                            <i class="fas fa-user"></i>
                                            <?= Html::encode($book->author_firstname . ' ' . $book->author_lastname) ?>
                                        </p>
                                        <?php if ($book->category): ?>
                                            <p class="category">
                                                <i class="fas fa-bookmark"></i>
                                                <?= Html::encode($book->category->name) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="book-actions">
                                        <?= Html::a('Подробнее', ['book/view', 'id' => $book->id], [
                                            'class' => 'btn btn-primary btn-sm',
                                            'onclick' => 'event.stopPropagation();'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Сообщение, если нет избранных книг -->
                        <p class="no-books">У вас пока нет избранных книг</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Секция истории просмотров -->
            <div class="history-section">
                <div class="section-header">
                    <h2>История просмотров</h2>
                    <!-- Ссылка на полную историю -->
                    <?= Html::a('Показать все', ['site/history'], ['class' => 'show-all']) ?>
                </div>
                <!-- Сетка с книгами из истории -->
                <div class="books-grid">
                    <?php if (!empty($historyBooks)): ?>
                        <?php // Показываем только первые 3 книги из истории
                        foreach (array_slice($historyBooks, 0, 3) as $book): ?>
                            <!-- Карточка книги -->
                            <div class="book-card" data-book-url="<?= Url::to(['book/view', 'id' => $book->id]) ?>">
                                <div class="book-cover">
                                    <?php if ($book->cover_image && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $book->cover_image)): ?>
                                        <img src="<?= Yii::getAlias('@web/uploads/covers/') . Html::encode($book->cover_image) ?>" 
                                             alt="<?= Html::encode($book->title) ?>" 
                                             class="img-fluid">
                                    <?php else: ?>
                                        <div class="no-cover">
                                            <i class="fas fa-book fa-3x"></i>
                                            <span>Нет обложки</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="book-info">
                                    <h3><?= Html::encode($book->title) ?></h3>
                                    <div class="book-details">
                                        <p class="author">
                                            <i class="fas fa-user"></i>
                                            <?= Html::encode($book->author_firstname . ' ' . $book->author_lastname) ?>
                                        </p>
                                        <?php if ($book->category): ?>
                                            <p class="category">
                                                <i class="fas fa-bookmark"></i>
                                                <?= Html::encode($book->category->name) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="book-actions">
                                        <?= Html::a('Подробнее', ['book/view', 'id' => $book->id], [
                                            'class' => 'btn btn-primary btn-sm',
                                            'onclick' => 'event.stopPropagation();'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Сообщение, если история пуста -->
                        <p class="no-books">Вы еще не просматривали книги</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Добавляем элемент для подсказки -->
<div id="tooltip"></div>
<style>
.book-card {
    cursor: pointer;
    transition: transform 0.3s ease;
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

.book-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
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
    }, '.book-cover img');
});
JS;
$this->registerJs($js);
?>

<?php
// Подключаем JavaScript файл для функционала смены пароля
$this->registerJsFile('@web/js/change-password.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<?php 
// Начало модального окна для смены пароля
Modal::begin([
    'id' => 'changePasswordModal',
    'title' => 'Изменение пароля',
    'size' => Modal::SIZE_DEFAULT,
]); ?>

<?php 
// Создаем форму для смены пароля
$form = \yii\bootstrap5\ActiveForm::begin([
    'id' => 'change-password-form',
    'action' => ['site/change-password'],
    'enableClientValidation' => false,
    'validateOnType' => false,
    'validateOnChange' => false,
    'options' => ['class' => 'change-password-form'],
]); ?>

<!-- Контейнер для отображения ошибок -->
<div class="alert alert-danger" style="display: none;"></div>

<!-- Поля формы смены пароля -->
<?= $form->field($passwordForm, 'current_password')->passwordInput() ?>
<?= $form->field($passwordForm, 'new_password')->passwordInput() ?>
<?= $form->field($passwordForm, 'new_password_repeat')->passwordInput() ?>

<!-- Кнопки формы -->
<div class="form-group text-right">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
</div>

<?php 
// Закрываем форму
\yii\bootstrap5\ActiveForm::end(); ?>
<?php 
// Закрываем модальное окно
Modal::end(); ?>
