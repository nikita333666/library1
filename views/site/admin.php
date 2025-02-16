<?php
/**
 * Панель администратора
 * Отображает основную статистику и элементы управления для администратора
 *
 * @var yii\web\View $this Текущее представление
 * @var array $stats Массив со статистикой (количество пользователей, книг, категорий)
 */

// Подключаем необходимые классы Yii2
use yii\helpers\Html;
use yii\helpers\Url;

// Устанавливаем заголовок страницы и добавляем его в "хлебные крошки"
$this->title = 'Панель администратора';
$this->params['breadcrumbs'][] = $this->title;

// Проверяем права доступа: если пользователь не авторизован или не админ - редирект на страницу входа
if (Yii::$app->user->isGuest || !Yii::$app->user->identity->is_admin) {
    return Yii::$app->response->redirect(['site/login']);
}
?>

<!-- Основной контейнер панели администратора -->
<div class="admin-panel">
    <div class="row">
        <!-- Левая колонка с навигационным меню -->
        <div class="col-md-3">
            <div class="list-group">
                <!-- Заголовок навигационного меню -->
                <div class="list-group-item active">
                    <h4>Управление</h4>
                </div>
                <!-- Ссылки на различные разделы админ-панели -->
                <a href="<?= Url::to(['/admin/books']) ?>" class="list-group-item list-group-item-action">
                    <i class="fas fa-book"></i> Управление книгами
                </a>
                <a href="<?= Url::to(['/blog/index']) ?>" class="list-group-item list-group-item-action">
                    <i class="fas fa-newspaper"></i> Управление блогом
                </a>
                <a href="<?= Url::to(['/admin/users']) ?>" class="list-group-item list-group-item-action">
                    <i class="fas fa-users"></i> Управление пользователями
                </a>
                <a href="<?= Url::to(['/admin/categories']) ?>" class="list-group-item list-group-item-action">
                    <i class="fas fa-tags"></i> Управление категориями
                </a>
                <a href="<?= Url::to(['/site-editor/index']) ?>" class="list-group-item list-group-item-action">
                    <i class="fas fa-edit"></i> Редактор сайта
                </a>
            </div>
        </div>

        <!-- Правая колонка с основным контентом -->
        <div class="col-md-9">
            <!-- Блок с основными статистическими показателями -->
            <div class="row mb-4">
                <!-- Карточка с количеством пользователей -->
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Пользователей</h5>
                            <p class="card-text display-4"><?= $stats['users_count'] ?></p>
                        </div>
                    </div>
                </div>
                <!-- Карточка с количеством книг -->
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Книг</h5>
                            <p class="card-text display-4"><?= $stats['books_count'] ?></p>
                        </div>
                    </div>
                </div>
                <!-- Карточка с количеством категорий -->
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Категорий</h5>
                            <p class="card-text display-4"><?= $stats['categories_count'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Блок с последними действиями в системе -->
            <div class="row">
                <!-- Список последних зарегистрированных пользователей -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Последние пользователи</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <?php 
                                // Перебираем массив последних пользователей
                                foreach ($stats['latest_users'] as $user): ?>
                                    <li class="list-group-item">
                                        <i class="fas fa-user"></i>
                                        <?= Html::encode($user->username) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Список последних добавленных книг -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Последние добавленные книги</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <?php 
                                // Перебираем массив последних добавленных книг
                                foreach ($stats['latest_books'] as $book): ?>
                                    <li class="list-group-item">
                                        <i class="fas fa-book"></i>
                                        <?= Html::encode($book->title) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Определяем CSS стили для панели администратора
$css = <<<CSS
/* Добавляем тень к карточкам для визуального выделения */
.admin-panel .card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 1rem;
}

/* Задаем отступ для иконок в списке */
.admin-panel .list-group-item i {
    margin-right: 10px;
}

/* Убираем нижний отступ у заголовков карточек */
.admin-panel .card-header h5 {
    margin-bottom: 0;
}

/* Стилизуем числовые показатели статистики */
.display-4 {
    font-size: 2.5rem;
    font-weight: 300;
    line-height: 1.2;
}
CSS;

// Регистрируем CSS стили в представлении
$this->registerCss($css);
?>
