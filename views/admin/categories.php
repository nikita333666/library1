<?php
/**
 * Представление для отображения списка категорий в админ-панели
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

// Подключение необходимых компонентов Yii2
use yii\helpers\Html;
use yii\grid\GridView;

// Установка заголовка страницы и хлебных крошек
$this->title = 'Управление категориями';
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Основной контейнер для списка категорий -->
<div class="categories-index">
    <!-- Разделение страницы на две колонки -->
    <div class="row">
        <!-- Основная колонка с таблицей категорий -->
        <div class="col-md-8">
            <h1><?= Html::encode($this->title) ?></h1>

            <!-- Кнопка создания новой категории -->
            <p>
                <?= Html::a('Создать категорию', ['create-category'], ['class' => 'btn btn-success']) ?>
            </p>

            <!-- Виджет таблицы для отображения категорий -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    // Колонка с названием категории
                    'name',
                    [
                        // Колонка с кнопками действий
                        'class' => 'yii\grid\ActionColumn',
                        'contentOptions' => ['style' => 'width: 100px; text-align: center;'],
                        'template' => '{update} {delete}',
                        'buttons' => [
                            // Кнопка редактирования категории
                            'update' => function ($url, $model) {
                                return Html::a('<i class="fas fa-edit"></i>', ['update-category', 'id' => $model->id], [
                                    'title' => 'Редактировать',
                                    'class' => 'btn btn-primary btn-sm action-btn',
                                ]);
                            },
                            // Кнопка удаления категории с подтверждением
                            'delete' => function ($url, $model) {
                                return Html::a('<i class="fas fa-trash"></i>', ['delete-category', 'id' => $model->id], [
                                    'title' => 'Удалить',
                                    'class' => 'btn btn-danger btn-sm action-btn',
                                    'data' => [
                                        'confirm' => 'Вы уверены, что хотите удалить эту категорию?',
                                        'method' => 'post',
                                    ],
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>

        <!-- Боковая колонка для отображения уведомлений -->
        <div class="col-md-4">
            <?php 
            // Отображение сообщения об ошибке, если оно есть
            if (Yii::$app->session->hasFlash('error')): ?>
                <?php 
                    $errorMessage = Yii::$app->session->getFlash('error');
                    // Проверка, относится ли сообщение к категориям
                    if (strpos(strtolower($errorMessage), 'категори') !== false):
                ?>
                    <!-- Блок с сообщением об ошибке -->
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="alert-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="alert-message">
                            <strong>Внимание!</strong><br>
                            <?= $errorMessage ?>
                        </div>
                        <!-- Кнопка закрытия уведомления -->
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php 
            // Отображение сообщения об успехе, если оно есть
            if (Yii::$app->session->hasFlash('success')): ?>
                <?php 
                    $successMessage = Yii::$app->session->getFlash('success');
                    // Проверка, относится ли сообщение к категориям
                    if (strpos(strtolower($successMessage), 'категори') !== false):
                ?>
                    <!-- Блок с сообщением об успехе -->
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="alert-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="alert-message">
                            <?= $successMessage ?>
                        </div>
                        <!-- Кнопка закрытия уведомления -->
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Стили для оформления страницы -->
<style>
/* Стили для уведомлений */
.alert {
    position: relative;
    padding: 1rem 1rem 1rem 3rem;
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Стили для уведомления об ошибке */
.alert-danger {
    background-color: #fff3f3;
    color: #dc3545;
    border-left: 4px solid #dc3545;
}

/* Стили для уведомления об успехе */
.alert-success {
    background-color: #f0fff0;
    color: #28a745;
    border-left: 4px solid #28a745;
}

/* Стили для иконки в уведомлении */
.alert-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.25rem;
}

/* Стили для текста уведомления */
.alert-message {
    margin-left: 0.5rem;
}

/* Стили для кнопки закрытия уведомления */
.alert .close {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    font-size: 1.1rem;
    opacity: 0.5;
    cursor: pointer;
}

.alert .close:hover {
    opacity: 1;
}

/* Стили для кнопок действий в таблице */
.action-btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    margin: 0 2px;
    min-width: 32px;
}

/* Стили для таблицы категорий */
.grid-view {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* Стили для заголовков таблицы */
.grid-view .table th {
    border-top: none;
}
</style>

<?php
// JavaScript для обработки закрытия уведомлений
$js = <<<JS
    // Функция для закрытия уведомления по ID
    function closeAlert(alertId) {
        document.getElementById(alertId).style.display = 'none';
    }
    
    // Добавление обработчиков для всех кнопок закрытия при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        var closeButtons = document.querySelectorAll('.alert .close');
        closeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var alert = this.closest('.alert');
                if (alert) {
                    alert.style.display = 'none';
                }
            });
        });
    });
JS;
// Регистрация JavaScript-кода для выполнения после загрузки страницы
$this->registerJs($js, \yii\web\View::POS_END);
?>
