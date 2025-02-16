<?php
// Подключаем необходимые классы Yii2
use yii\helpers\Html;
use yii\helpers\Url;

// Устанавливаем заголовок страницы
$this->title = 'Редактор сайта';
?>

<!-- Основной контейнер редактора сайта -->
<div class="site-editor">
    <!-- Заголовок страницы с иконкой -->
    <h1><i class="fas fa-edit"></i> <?= Html::encode($this->title) ?></h1>

    <!-- Сетка с карточками разделов -->
    <div class="row">
        <!-- Карточка редактирования главной страницы -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Иконка главной страницы -->
                    <i class="fas fa-home fa-3x mb-3"></i>
                    <!-- Заголовок карточки -->
                    <h5>Главная страница</h5>
                    <!-- Описание раздела -->
                    <p class="text-muted">Редактирование содержимого, баннеров и основных блоков главной страницы сайта</p>
                    <!-- Кнопка перехода к редактированию -->
                    <?= Html::a('РЕДАКТИРОВАТЬ', ['page-editor/index'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>

        <!-- Карточка редактирования страницы "О нас" -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Иконка страницы "О нас" -->
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <!-- Заголовок карточки -->
                    <h5>Страница "О нас"</h5>
                    <!-- Описание раздела -->
                    <p class="text-muted">Управление информацией о библиотеке, контактами и описанием сервиса, информация о компании</p>
                    <!-- Кнопка перехода к редактированию -->
                    <?= Html::a('РЕДАКТИРОВАТЬ', ['page-editor/about'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>

        <!-- Карточка SEO настроек -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Иконка SEO настроек -->
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <!-- Заголовок карточки -->
                    <h5>SEO настройки</h5>
                    <!-- Описание раздела -->
                    <p class="text-muted">Настройка мета-тегов, ключевых слов и оптимизация для поисковых систем, редактирование заголовков страниц</p>
                    <!-- Кнопка перехода к настройкам -->
                    <?= Html::a('НАСТРОИТЬ', ['seo/index'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Регистрируем CSS стили для оформления страницы
$this->registerCss("
    /* Стили для карточек */
    .site-editor .card {
        margin-bottom: 20px;
        transition: transform 0.2s;
    }

    /* Эффект при наведении на карточку */
    .site-editor .card:hover {
        transform: translateY(-5px);
    }

    /* Отступы внутри карточки */
    .site-editor .card-body {
        padding: 2rem;
    }

    /* Стили для кнопок */
    .site-editor .btn {
        margin-top: 1rem;
        padding: 0.5rem 2rem;
        text-transform: uppercase;
        font-weight: bold;
    }

    /* Цвет иконок */
    .site-editor i.fas {
        color: #0066cc;
    }
");
?>
