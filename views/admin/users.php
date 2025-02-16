<?php
/**
 * Представление для управления пользователями
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

// Подключение необходимых компонентов Yii2
use yii\helpers\Html;
use yii\grid\GridView;

// Установка заголовка страницы
$this->title = 'Управление пользователями';
// Формирование хлебных крошек для навигации
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Основной контейнер страницы управления пользователями -->
<div class="admin-users">
    <!-- Блок с заголовком страницы -->
    <div class="admin-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <!-- Кнопка создания нового пользователя -->
    <p>
        <?= Html::a('Создать пользователя', ['create-user'], ['class' => 'btn btn-success']) ?>
    </p>

    <!-- Таблица со списком пользователей -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'username',
            'email',
            [
                'attribute' => 'is_admin',
                'label' => 'Роль',
                // Форматирование отображения роли пользователя
                'value' => function($model) {
                    switch ($model->is_admin) {
                        case 2:
                            return 'Владелец';
                        case 1:
                            return 'Администратор';
                        default:
                            return 'Пользователь';
                    }
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'white-space: nowrap; text-align: center; width: 90px;'],
                'template' => '{update} {delete}',
                'buttons' => [
                    // Кнопка редактирования пользователя
                    'update' => function ($url, $model, $key) {
                        // Не показываем кнопку редактирования для текущего пользователя
                        if (Yii::$app->user->id == $model->id) {
                            return '';
                        }
                        
                        // Владелец может редактировать админов, но не других владельцев
                        if (Yii::$app->user->identity->is_admin == 2) {
                            if ($model->is_admin == 2) {
                                return '';
                            }
                            return Html::a('<i class="fas fa-edit"></i>', ['update-user', 'id' => $model->id], [
                                'title' => 'Редактировать',
                                'class' => 'btn btn-link p-0 mr-2'
                            ]);
                        }
                        // Обычные админы не могут редактировать других админов
                        if ($model->is_admin) {
                            return '';
                        }
                        return Html::a('<i class="fas fa-edit"></i>', ['update-user', 'id' => $model->id], [
                            'title' => 'Редактировать',
                            'class' => 'btn btn-link p-0 mr-2'
                        ]);
                    },
                    // Кнопка удаления пользователя
                    'delete' => function ($url, $model, $key) {
                        // Владелец может удалять админов, но не других владельцев
                        if (Yii::$app->user->identity->is_admin == 2) {
                            if ($model->is_admin == 2) {
                                return '';
                            }
                            return Html::a('<i class="fas fa-trash"></i>', ['delete-user', 'id' => $model->id], [
                                'title' => 'Удалить',
                                'class' => 'btn btn-link p-0 text-danger',
                                'data' => [
                                    'confirm' => 'Вы уверены, что хотите удалить этого пользователя?',
                                    'method' => 'post',
                                ],
                            ]);
                        }
                        // Обычные админы не могут удалять админов
                        if ($model->is_admin) {
                            return '';
                        }
                        return Html::a('<i class="fas fa-trash"></i>', ['delete-user', 'id' => $model->id], [
                            'title' => 'Удалить',
                            'class' => 'btn btn-link p-0 text-danger',
                            'data' => [
                                'confirm' => 'Вы уверены, что хотите удалить этого пользователя?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>

<?php
// Регистрация CSS-стилей для оформления страницы
$this->registerCss("
    /* Стили для отображения роли пользователя */
    .user-role {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.9em;
        font-weight: 500;
    }
    
    /* Стиль для роли администратора */
    .admin-role {
        background-color: #28a745;
        color: white;
    }
    
    /* Стиль для роли пользователя */
    .user-role {
        background-color: #6c757d;
        color: white;
    }
    
    /* Выравнивание текста по правому краю */
    .text-right {
        text-align: right !important;
    }
    
    /* Отступ для заголовка */
    .admin-header {
        margin-bottom: 2rem;
    }
    
    /* Стили для кнопок действий */
    .btn-link {
        text-decoration: none;
        border: none;
        background: none;
        padding: 0;
        margin: 0 3px;
    }
    .btn-link:hover {
        text-decoration: none;
        opacity: 0.8;
    }
    .fa-edit {
        color: #007bff;
    }
    .fa-trash {
        color: #dc3545;
    }
");
?>
