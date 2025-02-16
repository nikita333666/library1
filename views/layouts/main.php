<?php
/**
 * Главный шаблон приложения
 * Содержит общую структуру HTML, навигацию и подвал сайта
 * Этот файл является основным макетом для всех страниц сайта
 * 
 * @var $this \yii\web\View Объект представления Yii2
 * @var $content string Содержимое страницы, которое будет вставлено в шаблон
 */

// Подключаем необходимые классы Yii2 для работы с HTML, URL и ресурсами
use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\models\SeoSettings;

// Регистрируем основной пакет ресурсов приложения (CSS, JavaScript)
AppAsset::register($this);

// Получаем текущий маршрут (контроллер/действие)
$currentRoute = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;

// Проверяем, является ли текущая страница частью админ-панели
$isAdminPage = Yii::$app->controller->module->id === 'admin' || 
               strpos($currentRoute, 'admin') !== false || 
               $currentRoute === 'seo/index' || 
               $currentRoute === 'seo/update';

// Выводим отладочную информацию, если включен режим разработки
if (YII_DEBUG) {
    echo "<!-- Debug Info:\n";
    echo "Current Route: " . $currentRoute . "\n";
    echo "-->";
}

// Настройка SEO мета-тегов для не-административных страниц
if (!$isAdminPage) {
    // Пытаемся получить SEO-настройки из сессии
    $session = Yii::$app->session;
    $sessionTitle = $session->get("seo_{$currentRoute}_title");
    $sessionDescription = $session->get("seo_{$currentRoute}_description");

    // Если есть данные в сессии, используем их
    if ($sessionTitle !== null) {
        $this->title = $sessionTitle;
    }
    if ($sessionDescription !== null) {
        $this->registerMetaTag([
            'name' => 'description',
            'content' => $sessionDescription
        ], 'description');
    } else {
        // Если в сессии нет данных, пытаемся получить из базы данных
        $seoSettings = SeoSettings::findOne(['page_url' => $currentRoute]);
        if ($seoSettings) {
            if (empty($this->title)) {
                $this->title = $seoSettings->title;
            }
            $this->registerMetaTag([
                'name' => 'description',
                'content' => $seoSettings->description
            ], 'description');
        }
    }
}

// Дополнительная проверка и применение SEO настроек напрямую из базы
$route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
$seoSettings = SeoSettings::findOne(['page_url' => $route]);
if ($seoSettings && !empty($seoSettings->title)) {
    $this->title = $seoSettings->title;
}
if ($seoSettings && !empty($seoSettings->description)) {
    $this->registerMetaTag(['name' => 'description', 'content' => $seoSettings->description], 'description');
}

?>
<?php // Начало формирования страницы
$this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language // Устанавливаем язык сайта ?>">
<head>
    <!-- Метатеги для правильного отображения и SEO -->
    <meta charset="<?= Yii::$app->charset // Кодировка сайта ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= Html::csrfMetaTags() // Защита от CSRF-атак ?>
    <title><?= Html::encode($this->title) // Безопасный вывод заголовка страницы ?></title>
    <?php $this->head() // Вывод дополнительных метатегов и скриптов ?>
    
    <!-- Подключение основных CSS стилей сайта -->
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/main.css') ?>">
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/books.css') ?>">
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/book-view.css') ?>">
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/comments.css') ?>">
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/library.css') ?>">
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/blog.css') ?>"> <!-- Добавляем стили для блога -->
    
    <!-- Подключение внешних библиотек -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php $this->beginBody() // Начало тела страницы ?>

<!-- Основной контейнер сайта -->
<div class="wrap">
    <!-- Главное навигационное меню -->
    <nav class="navbar">
        <div class="navbar-container">
            <!-- Логотип -->
            <div class="logo">
                <a href="<?= Url::to(['/site/index']) ?>">
                    <i class="fas fa-book-reader"></i>
                    <span>МирЗнаний</span>
                </a>
            </div>

            <!-- Кнопка мобильного меню -->
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Основное меню -->
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="<?= Url::to(['/site/index']) ?>">Главная</a></li>
                    <li><a href="<?= Url::to(['/book/books']) ?>">Библиотека</a></li>
                    <li><a href="<?= Url::to(['/blog/index']) ?>">Блог</a></li>
                    <li><a href="<?= Url::to(['/site/about']) ?>">О нас</a></li>
                </ul>

                <!-- Кнопки авторизации -->
                <div class="nav-auth">
                    <?php if (Yii::$app->user->isGuest): ?>
                        <a href="<?= Url::to(['/site/signup']) ?>" class="nav-button signup">Регистрация</a>
                        <a href="<?= Url::to(['/site/login']) ?>" class="nav-button login">Войти</a>
                    <?php else: ?>
                        <?php if (!Yii::$app->user->isGuest && (Yii::$app->user->identity->is_admin == 1 || Yii::$app->user->identity->is_admin == 2)): ?>
                            <a href="<?= Url::to(['/site/admin']) ?>" class="nav-button admin">Админ панель</a>
                        <?php endif; ?>
                        <div class="user-dropdown">
                            <button class="dropbtn" id="userMenuBtn">
                                <?= Html::encode(Yii::$app->user->identity->username) ?>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-content" id="userDropdown">
                                <a href="<?= Url::to(['/site/profile']) ?>">
                                    <i class="fas fa-user"></i>
                                    Мой профиль
                                </a>
                                <a href="<?= Url::to(['/site/logout']) ?>" data-method="post">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Выйти
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <style>
        /* Стили для навбара */
        .navbar {
            background-color: #2c2c2c;
            padding: 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0.8rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.4rem;
            font-weight: bold;
            color: #00ff95;
            text-decoration: none;
        }

        .logo i {
            color: #00ff95; /* Теперь такой же цвет как и текст */
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-menu ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-menu a {
            color: #ffffff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-menu a:hover {
            color: #4CAF50;
        }

        .nav-auth {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-button {
            padding: 0.6rem 1.2rem;
            border-radius: 4px;
            background-color: #4CAF50;
            color: #ffffff !important;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .nav-button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        .user-dropdown {
            position: relative;
        }

        .dropbtn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: none;
            border: none;
            color: #ffffff;
            cursor: pointer;
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #2c2c2c;
            min-width: 200px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            padding: 0.5rem 0;
        }

        .dropdown-content.show {
            display: block;
        }

        .dropdown-content a {
            color: #ffffff;
            padding: 0.8rem 1.2rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .dropdown-content a:hover {
            background-color: #3c3c3c;
        }

        /* Медиа-запросы для адаптивности */
        @media (max-width: 992px) {
            .mobile-menu-btn {
                display: block;
            }

            .nav-menu {
                display: none;
                position: fixed;
                top: 60px; /* Высота навбара */
                right: 0;
                width: 300px; /* Фиксированная ширина меню */
                height: calc(100vh - 60px); /* Высота на весь экран минус навбар */
                background-color: #2c2c2c;
                padding: 1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
                overflow-y: auto; /* Добавляем скролл если контент не помещается */
                transform: translateX(100%);
                transition: transform 0.3s ease;
            }

            .nav-menu.show {
                display: flex;
                transform: translateX(0);
            }

            .nav-menu ul {
                flex-direction: column;
                width: 100%;
                align-items: flex-start;
            }

            .nav-menu li {
                width: 100%;
            }

            .nav-menu a {
                display: block;
                padding: 0.8rem 0;
            }

            .nav-auth {
                flex-direction: column;
                width: 100%;
                gap: 0.8rem;
            }

            .nav-button {
                width: 100%;
                text-align: center;
            }

            .user-dropdown {
                width: 100%;
            }

            .dropbtn {
                width: 100%;
                justify-content: space-between;
            }

            .dropdown-content {
                position: static;
                width: 100%;
                margin-top: 0.5rem;
            }
        }

        /* Стили для футера */
        .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.4rem;
            color: #00ff95;
            text-decoration: none !important; /* Убираем подчеркивание */
        }

        .footer-logo:hover {
            text-decoration: none !important; /* Убираем подчеркивание при наведении */
            color: #00ff95;
        }

        .footer-logo i {
            font-size: 1.6rem;
            color: #00ff95; /* Такой же цвет как и текст */
        }
    </style>

    <!-- JavaScript для управления мобильным меню и дропдауном -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Мобильное меню
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const navMenu = document.getElementById('navMenu');

            mobileMenuBtn.addEventListener('click', function() {
                navMenu.classList.toggle('show');
            });

            // Дропдаун пользователя
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userDropdown = document.getElementById('userDropdown');

            if (userMenuBtn && userDropdown) {
                userMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('show');
                });

                // Закрытие дропдауна при клике вне его
                document.addEventListener('click', function(e) {
                    if (!userDropdown.contains(e.target) && !userMenuBtn.contains(e.target)) {
                        userDropdown.classList.remove('show');
                    }
                });
            }

            // Закрытие мобильного меню при клике вне его
            document.addEventListener('click', function(e) {
                if (!navMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                    navMenu.classList.remove('show');
                }
            });
        });
    </script>

    <!-- Основное содержимое страницы -->
    <div class="container">
        <?= $content // Вставка контента конкретной страницы ?>
    </div>
</div>

<!-- Подвал сайта -->
<footer class="site-footer">
    <div class="footer-content">
        <a href="<?= Url::to(['/site/index']) ?>" class="footer-logo">
            <i class="fas fa-book-reader"></i>
            <span>МирЗнаний</span>
        </a>
        <div class="footer-section">
            <h3>О нас</h3>
            <p>МирЗнаний - это современная электронная библиотека, предоставляющая доступ к разнообразной литературе.</p>
        </div>
        <div class="footer-section">
            <h3>Навигация</h3>
            <ul>
                <li><a href="<?= Url::to(['/site/index']) ?>">Главная</a></li>
                <li><a href="<?= Url::to(['/book/books']) ?>">Библиотека</a></li>
                <li><a href="<?= Url::to(['/blog/index']) ?>">Блог</a></li>
                <li><a href="<?= Url::to(['/site/about']) ?>">О нас</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Контакты</h3>
            <p>Email: info@mirznanii.ru</p>
            <p>Телефон: +7 (000) 000-00-00</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> МирЗнаний. Все права защищены.</p>
    </div>
</footer>

<?php $this->endBody() // Завершение тела страницы ?>
</body>
</html>
<?php $this->endPage() // Завершение страницы ?>