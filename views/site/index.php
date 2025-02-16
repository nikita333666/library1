<?php
// Подключаем необходимые классы Yii2
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\PageContent;

// Устанавливаем заголовок страницы и мета-описание
$this->title = 'Библиотека';
$this->params['metaDescription'] = 'Добро пожаловать в нашу онлайн библиотеку! У нас вы найдете огромную коллекцию книг различных жанров. Читайте, скачивайте и наслаждайтесь чтением.';

// Получаем текущий маршрут
$currentRoute = Yii::$app->controller->route;
Yii::debug("Current route: " . $currentRoute);

// Получаем контент из базы данных
$welcomeTitle = PageContent::getContent($currentRoute, 'welcome_title');
$welcomeSubtitle = PageContent::getContent($currentRoute, 'welcome_subtitle');
$feature1Title = PageContent::getContent($currentRoute, 'feature_1_title');
$feature1Text = PageContent::getContent($currentRoute, 'feature_1_text');
$feature2Title = PageContent::getContent($currentRoute, 'feature_2_title');
$feature2Text = PageContent::getContent($currentRoute, 'feature_2_text');
$feature3Title = PageContent::getContent($currentRoute, 'feature_3_title');
$feature3Text = PageContent::getContent($currentRoute, 'feature_3_text');

// Выводим отладочную информацию
Yii::debug([
    'welcomeTitle' => $welcomeTitle,
    'welcomeSubtitle' => $welcomeSubtitle,
    'feature1Title' => $feature1Title,
    'feature1Text' => $feature1Text,
    'currentRoute' => $currentRoute
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Мета-теги для адаптивности и SEO -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/library.css">
    <script src="js/library.js" defer></script>
    <meta name="description" content="<?= Html::encode($this->params['metaDescription']) ?>">
</head>
<body>
    <main>
        <!-- Основной контейнер сайта -->
        <div class="site-container">
            <!-- Приветственная секция с заголовком и подзаголовком -->
            <div class="welcome-section">
                <h1 class="display-4">
                    <?= Html::encode($welcomeTitle ?: 'Добро пожаловать в нашу библиотеку!') ?>
                </h1>
                <p class="lead">
                    <?= Html::encode($welcomeSubtitle ?: 'Откройте для себя мир книг') ?>
                </p>
                
                <!-- Сетка с преимуществами сервиса -->
                <div class="features-grid">
                    <!-- Первая карточка преимущества -->
                    <div class="feature-item">
                        <i class="fas fa-book-reader"></i>
                        <h3>
                            <?= Html::encode($feature1Title ?: 'Безграничный доступ') ?>
                        </h3>
                        <p>
                            <?= Html::encode($feature1Text ?: 'Более 1000 книг различных жанров в вашем распоряжении') ?>
                        </p>
                    </div>

                    <!-- Вторая карточка преимущества -->
                    <div class="feature-item">
                        <i class="fas fa-mobile-alt"></i>
                        <h3>
                            <?= Html::encode($feature2Title ?: 'Читайте где угодно') ?>
                        </h3>
                        <p>
                            <?= Html::encode($feature2Text ?: 'Доступ к библиотеке с любого устройства 24/7') ?>
                        </p>
                    </div>

                    <!-- Третья карточка преимущества -->
                    <div class="feature-item">
                        <i class="fas fa-bookmark"></i>
                        <h3>
                            <?= Html::encode($feature3Title ?: 'Бесплатно') ?>
                        </h3>
                        <p>
                            <?= Html::encode($feature3Text ?: 'Все книги доступны бесплатно после регистрации') ?>
                        </p>
                    </div>
                </div>

                <?php 
                // Показываем кнопки регистрации/входа только для гостей
                if (Yii::$app->user->isGuest): ?>
                    <div class="cta-buttons">
                        <p>Присоединяйтесь к нашему сообществу читателей прямо сейчас!</p>
                        <div class="buttons-wrapper">
                            <?= Html::a('Зарегистрироваться', ['site/signup'], ['class' => 'btn btn-primary btn-lg']) ?>
                            <?= Html::a('Войти', ['site/login'], ['class' => 'btn btn-primary btn-lg']) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Основной контент с книгами -->
            <div class="main-content">
                <!-- Секция самых читаемых книг -->
                <section class="books-section">
                    <div class="section-header">
                        <h2>Самые читаемые книги</h2>
                    </div>
                    <!-- Сетка с карточками популярных книг -->
                    <div class="books-grid">
                        <?php foreach ($topViewed as $book): ?>
                            <!-- Карточка отдельной книги -->
                            <div class="book-card" style="display: block;" >
                                <a href="<?= Url::to(['book/view', 'id' => $book->seo_url ?: $book->generateSeoUrl($book->title)]) ?>" 
                                   style="text-decoration: none; color: inherit; border: none; outline: none; display: block;">
                                <!-- Обложка книги -->
                                <div class="book-cover">
                                    <?php 
                                    // Проверяем наличие обложки
                                    if ($book->cover_image && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $book->cover_image)): ?>
                                        <img src="<?= Yii::getAlias('@web/uploads/covers/') . Html::encode($book->cover_image) ?>" 
                                             alt="<?= Html::encode($book->image_alt ?: 'Обложка книги ' . $book->title) ?>" 
                                             class="cover-image">
                                    <?php else: ?>
                                        <!-- Заглушка, если нет обложки -->
                                        <div class="no-cover">
                                            <i class="fas fa-book fa-3x"></i>
                                            <span>Нет обложки</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <!-- Информация о книге -->
                                <div class="book-info">
                                    <h3><?= Html::encode($book->title) ?></h3>
                                    <p class="author"><?= Html::encode($book->getAuthorName()) ?></p>
                                    <p class="views"><i class="fas fa-eye"></i> <?= $book->views ?></p>
                                    <?= Html::a('ПОДРОБНЕЕ', ['book/view', 'id' => $book->id], ['class' => 'btn btn-primary btn-sm']) ?>
                                </div>
                            </div></a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Секция рекомендуемых книг -->
                <section class="books-section">
                    <div class="section-header">
                        <h2>Рекомендуем к прочтению</h2>
                    </div>
                    <!-- Сетка с карточками рекомендуемых книг -->
                    <div class="books-grid">
                        <?php foreach ($recommendedBooks as $book): ?>
                            <!-- Карточка отдельной книги -->
                            <div class="book-card" style="display: block;" >
                                <a href="<?= Url::to(['book/view', 'id' => $book->seo_url ?: $book->generateSeoUrl($book->title)]) ?>" 
                                   style="text-decoration: none; color: inherit; border: none; outline: none; display: block;">
                                <!-- Обложка книги -->
                                <div class="book-cover">
                                    <?php if ($book->cover_image && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $book->cover_image)): ?>
                                        <img src="<?= Yii::getAlias('@web/uploads/covers/') . Html::encode($book->cover_image) ?>" 
                                             alt="<?= Html::encode($book->image_alt) ?>">
                                    <?php else: ?>
                                        <!-- Заглушка, если нет обложки -->
                                        <div class="no-cover">
                                            <i class="fas fa-book fa-3x"></i>
                                            <span>Нет обложки</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <!-- Информация о книге -->
                                <div class="book-info">
                                    <h3><?= Html::encode($book->title) ?></h3>
                                    <p class="author"><?= Html::encode($book->getAuthorName()) ?></p>
                                    <div class="actions">
                                        <?= Html::a('ПОДРОБНЕЕ', ['book/view', 'id' => $book->id], ['class' => 'btn btn-primary btn-sm']) ?>
                                    </div>
                                </div>
                                </div></a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Секция новых поступлений -->
                <section class="books-section">
                    <div class="section-header">
                        <h2>Новые поступления</h2>
                    </div>
                    <!-- Сетка с карточками новых книг -->
                    <div class="books-grid">
                        <?php foreach ($newBooks as $book): ?>
                            <!-- Карточка отдельной книги -->
                            <div class="book-card" style="display: block;" >
                                <a href="<?= Url::to(['book/view', 'id' => $book->seo_url ?: $book->generateSeoUrl($book->title)]) ?>" 
                                   style="text-decoration: none; color: inherit; border: none; outline: none; display: block;">
                                <!-- Обложка книги -->
                                <div class="book-cover">
                                    <?php if ($book->cover_image && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $book->cover_image)): ?>
                                        <img src="<?= Yii::getAlias('@web/uploads/covers/') . Html::encode($book->cover_image) ?>" 
                                             alt="<?= Html::encode($book->image_alt ?: 'Обложка книги ' . $book->title) ?>" 
                                             class="cover-image">
                                    <?php else: ?>
                                        <!-- Заглушка, если нет обложки -->
                                        <div class="no-cover">
                                            <i class="fas fa-book fa-3x"></i>
                                            <span>Нет обложки</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="book-info">
                                    <h3><?= Html::encode($book->title) ?></h3>
                                    <p class="author"><?= Html::encode($book->getAuthorName()) ?></p>
                                    <?= Html::a('ПОДРОБНЕЕ', ['book/view', 'id' => $book->id], ['class' => 'btn btn-primary btn-sm']) ?>
                                </div>
                                </div></a>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </div>

        <!-- Стили для оформления страницы -->
        <style>
        /* Основной контейнер сайта */
        .site-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Стили для приветственной секции */
        .welcome-section {
            text-align: center;
            margin: 0 auto 50px;
            padding: 40px 20px;
            max-width: 1000px;
        }

        /* Заголовок приветственной секции */
        .welcome-section h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2.5em;
        }

        /* Подзаголовок приветственной секции */
        .welcome-section .lead {
            color: #34495e;
            font-size: 1.2em;
            margin-bottom: 40px;
        }

        /* Сетка с преимуществами */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 40px auto;
            max-width: 1000px;
        }

        /* Карточка преимущества */
        .feature-item {
            padding: 30px;
            text-align: center;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        /* Эффект при наведении на карточку */
        .feature-item:hover {
            transform: translateY(-5px);
        }

        /* Иконка в карточке преимущества */
        .feature-item i {
            font-size: 2.5em;
            color: #007bff;
            margin-bottom: 20px;
        }

        /* Заголовок карточки преимущества */
        .feature-item h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        /* Текст карточки преимущества */
        .feature-item p {
            color: #666;
            line-height: 1.6;
        }

        /* Блок с кнопками призыва к действию */
        .cta-buttons {
            margin-top: 40px;
        }

        /* Текст над кнопками */
        .cta-buttons p {
            margin-bottom: 20px;
            color: #666;
        }

        /* Контейнер для кнопок */
        .buttons-wrapper {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        /* Общие стили для кнопок */
        .btn {
            padding: 12px 30px;
            font-size: 1.1em;
            border-radius: 25px;
            transition: all 0.3s;
            background: #007bff;
            color: white;
            border: none;
        }

        /* Эффект при наведении на кнопку */
        .btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        /* Стили для основной кнопки */
        .btn-primary {
            background: #007bff;
        }

        /* Эффект при наведении на основную кнопку */
        .btn-primary:hover {
            background: #0056b3;
        }

        /* Стили для большой кнопки */
        .btn-lg {
            padding: 15px 40px;
            font-size: 1.3em;
        }

        /* Стили для маленькой кнопки */
        .btn-sm {
            padding: 8px 20px;
            font-size: 0.9em;
        }

        /* Секция с книгами */
        .books-section {
            margin-bottom: 60px;
        }

        /* Шапка секции с книгами */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 0 10px;
        }

        /* Заголовок секции */
        .section-header h2 {
            color: #2c3e50;
            margin: 0;
            font-size: 1.8em;
        }

        /* Ссылка "Смотреть все" */
        .view-all {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        /* Эффект при наведении на ссылку */
        .view-all:hover {
            color: #0056b3;
            text-decoration: none;
        }

        /* Сетка с книгами */
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin: 0 auto;
            max-width: 1200px;
        }

        /* Адаптивная верстка для больших экранов */
        @media (min-width: 1200px) {
            .books-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Адаптивная верстка для средних экранов */
        @media (max-width: 1199px) and (min-width: 992px) {
            .books-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Адаптивная верстка для планшетов */
        @media (max-width: 991px) and (min-width: 768px) {
            .books-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Адаптивная верстка для мобильных устройств */
        @media (max-width: 767px) {
            .books-grid {
                grid-template-columns: repeat(1, 1fr);
                max-width: 400px;
            }
        }

        /* Дополнительные стили для больших экранов */
        @media (min-width: 1200px) {
            .books-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Дополнительные стили для мобильных устройств */
        @media (max-width: 768px) {
            .welcome-section {
                padding: 30px 15px;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .buttons-wrapper {
                flex-direction: column;
                gap: 15px;
            }

            .books-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }
        </style>
    </main>
</body>
</html>
