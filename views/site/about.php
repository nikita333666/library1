<?php
// Определяем, что переменная $this является экземпляром класса yii\web\View
/** @var yii\web\View $this */

// Подключаем необходимые классы из Yii2 framework
use yii\helpers\Html;
use app\models\PageContent;

// Устанавливаем заголовок страницы
$this->title = 'О нас';
// Устанавливаем мета-описание для SEO
$this->params['metaDescription'] = 'Узнайте больше о нашей библиотеке. Мы предоставляем доступ к огромной коллекции книг, создаем комфортную среду для чтения и развиваем любовь к литературе.';
// Добавляем текущий заголовок в "хлебные крошки"
$this->params['breadcrumbs'][] = $this->title;

// Получаем текущий маршрут
$currentRoute = Yii::$app->controller->route;

// Вспомогательная функция для получения контента из базы данных
function getContent($section, $identifier, $default = '') {
    $content = PageContent::getContent('site/about', $section . '_' . $identifier);
    return $content ?: $default;
}
?>

<!-- Основной контейнер страницы "О нас" -->
<div class="about-page">
    <!-- Секция с главным заголовком (hero section) -->
    <div class="hero-section">
        <h1><?= Html::encode(getContent('hero', 'title', 'Добро пожаловать в мир книг!')) ?></h1>
        <p class="subtitle"><?= Html::encode(getContent('hero', 'subtitle', 'Где каждая страница открывает новую вселенную')) ?></p>
    </div>

    <!-- Основной контент страницы -->
    <div class="about-content">
        <!-- Секция приветствия -->
        <section class="welcome-section">
            <h2><?= Html::encode(getContent('welcome', 'title', 'Ваша идеальная онлайн библиотека')) ?></h2>
            <p><?= Html::encode(getContent('welcome', 'text', 'Представьте место, где собраны тысячи увлекательных историй, ждущих именно вас. Место, где каждый найдет книгу по душе, независимо от вкусов и предпочтений.')) ?></p>
        </section>

        <!-- Секция жанров -->
        <section class="genres-section">
            <h2><?= Html::encode(getContent('genres', 'title', 'Богатство жанров')) ?></h2>
            <div class="genres-grid">
                <?php
                $genres = [
                    ['icon' => 'dragon', 'title' => 'Фэнтези', 'desc' => 'Погрузитесь в миры магии и приключений'],
                    ['icon' => 'rocket', 'title' => 'Научная фантастика', 'desc' => 'Исследуйте будущее человечества'],
                    ['icon' => 'heart', 'title' => 'Романтика', 'desc' => 'Переживите истории великой любви'],
                    ['icon' => 'mask', 'title' => 'Детективы', 'desc' => 'Раскройте самые загадочные тайны'],
                    ['icon' => 'book-reader', 'title' => 'Классика', 'desc' => 'Откройте бессмертные произведения'],
                    ['icon' => 'graduation-cap', 'title' => 'Образование', 'desc' => 'Получите новые знания']
                ];
                foreach ($genres as $index => $genre): ?>
                    <div class="genre-card">
                        <i class="fas fa-<?= $genre['icon'] ?>"></i>
                        <h3><?= Html::encode(getContent('genre', 'title_' . $index, $genre['title'])) ?></h3>
                        <p><?= Html::encode(getContent('genre', 'desc_' . $index, $genre['desc'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Секция преимуществ -->
        <section class="features-section">
            <h2><?= Html::encode(getContent('features', 'title', 'Почему выбирают нас?')) ?></h2>
            <div class="features-list">
                <?php
                $features = [
                    ['icon' => 'clock', 'title' => 'Доступ 24/7', 'desc' => 'Читайте в любое время, в любом месте'],
                    ['icon' => 'mobile-alt', 'title' => 'Удобный интерфейс', 'desc' => 'Адаптировано для всех устройств'],
                    ['icon' => 'sync', 'title' => 'Регулярные обновления', 'desc' => 'Новые книги каждую неделю']
                ];
                foreach ($features as $index => $feature): ?>
                    <div class="feature">
                        <i class="fas fa-<?= $feature['icon'] ?>"></i>
                        <h3><?= Html::encode(getContent('feature', 'title_' . $index, $feature['title'])) ?></h3>
                        <p><?= Html::encode(getContent('feature', 'desc_' . $index, $feature['desc'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Секция статистики -->
        <section class="stats-section">
            <h2><?= Html::encode(getContent('stats', 'title', 'Наша библиотека в цифрах')) ?></h2>
            <div class="stats-grid">
                <?php
                $stats = [
                    ['number' => '10,000+', 'label' => 'Книг'],
                    ['number' => '20+', 'label' => 'Жанров'],
                    ['number' => '5,000+', 'label' => 'Читателей'],
                    ['number' => '100+', 'label' => 'Новинок в месяц']
                ];
                foreach ($stats as $index => $stat): ?>
                    <div class="stat-card">
                        <span class="stat-number"><?= Html::encode(getContent('stat', 'number_' . $index, $stat['number'])) ?></span>
                        <span class="stat-label"><?= Html::encode(getContent('stat', 'label_' . $index, $stat['label'])) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>
