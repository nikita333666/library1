<?php
// Подключаем необходимые классы Yii2 для работы с HTML и URL
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\PageContent;

// Устанавливаем URL для обновления контента и возврата на главную
$updateUrl = Url::to(['site/update-content']);
$aboutUrl = Url::to(['site/about']);
$currentPage = 'site/about'; // Фиксированный маршрут для страницы about

// Устанавливаем заголовок страницы
$this->title = 'Редактор страницы О нас';

// Подключаем CSS файл для стилизации
$this->registerCssFile('@web/css/about.css');

// Регистрируем Bootstrap 5 для улучшенного интерфейса
$this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', [
    'depends' => [\yii\web\JqueryAsset::class]
]);

// Регистрируем SweetAlert2 для красивых уведомлений
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', [
    'depends' => [\yii\web\JqueryAsset::class]
]);
?>

<!-- Основной контейнер редактора страницы -->
<div class="page-editor">
    <!-- Шапка редактора с заголовком и кнопкой сохранения -->
    <div class="editor-header mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <!-- Информационное сообщение для пользователя -->
        <div class="alert alert-info">
            Нажмите на текст, чтобы изменить его содержимое
        </div>
        <!-- Кнопка для сохранения всех изменений -->
        <button class="btn btn-primary mb-3 w-100" onclick="saveAllChanges()">
            <i class="fas fa-save"></i> СОХРАНИТЬ ВСЕ ИЗМЕНЕНИЯ
        </button>
    </div>

    <!-- Основной контейнер страницы "О нас" -->
    <div class="about-page">
        <!-- Секция с главным заголовком (hero section) -->
        <div class="hero-section">
            <div class="editable-block">
                <h1 class="editable-content" data-identifier="hero_title" contenteditable="true">
                    <?= Html::encode(PageContent::getContent($currentPage, 'hero_title') ?: 'Добро пожаловать в мир книг!') ?>
                </h1>
                <p class="subtitle editable-content" data-identifier="hero_subtitle" contenteditable="true">
                    <?= Html::encode(PageContent::getContent($currentPage, 'hero_subtitle') ?: 'Где каждая страница открывает новую вселенную') ?>
                </p>
            </div>
        </div>

        <!-- Основной контент страницы -->
        <div class="about-content">
            <!-- Секция приветствия -->
            <section class="welcome-section">
                <div class="editable-block">
                    <h2 class="editable-content" data-identifier="welcome_title" contenteditable="true">
                        <?= Html::encode(PageContent::getContent($currentPage, 'welcome_title') ?: 'Ваша идеальная онлайн библиотека') ?>
                    </h2>
                    <p class="editable-content" data-identifier="welcome_text" contenteditable="true">
                        <?= Html::encode(PageContent::getContent($currentPage, 'welcome_text') ?: 'Представьте место, где собраны тысячи увлекательных историй, ждущих именно вас. Место, где каждый найдет книгу по душе, независимо от вкусов и предпочтений.') ?>
                    </p>
                </div>
            </section>

            <!-- Секция жанров -->
            <section class="genres-section">
                <div class="editable-block">
                    <h2 class="editable-content" data-identifier="genres_title" contenteditable="true">
                        <?= Html::encode(PageContent::getContent($currentPage, 'genres_title') ?: 'Богатство жанров') ?>
                    </h2>
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
                                <div class="editable-block">
                                    <h3 class="editable-content" data-identifier="genre_title_<?= $index ?>" contenteditable="true">
                                        <?= Html::encode(PageContent::getContent($currentPage, 'genre_title_' . $index) ?: $genre['title']) ?>
                                    </h3>
                                    <p class="editable-content" data-identifier="genre_desc_<?= $index ?>" contenteditable="true">
                                        <?= Html::encode(PageContent::getContent($currentPage, 'genre_desc_' . $index) ?: $genre['desc']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <!-- Секция преимуществ -->
            <section class="features-section">
                <div class="editable-block">
                    <h2 class="editable-content" data-identifier="features_title" contenteditable="true">
                        <?= Html::encode(PageContent::getContent($currentPage, 'features_title') ?: 'Почему выбирают нас?') ?>
                    </h2>
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
                                <div class="editable-block">
                                    <h3 class="editable-content" data-identifier="feature_title_<?= $index ?>" contenteditable="true">
                                        <?= Html::encode(PageContent::getContent($currentPage, 'feature_title_' . $index) ?: $feature['title']) ?>
                                    </h3>
                                    <p class="editable-content" data-identifier="feature_desc_<?= $index ?>" contenteditable="true">
                                        <?= Html::encode(PageContent::getContent($currentPage, 'feature_desc_' . $index) ?: $feature['desc']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
// Множество для хранения измененных элементов
let changedElements = new Set();

// Функция для сохранения отдельного элемента
function saveInlineContent(element, content) {
    const formData = new URLSearchParams();
    formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->csrfToken ?>');
    formData.append('identifier', element.dataset.identifier);
    formData.append('content', content);
    formData.append('page', '<?= $currentPage ?>');

    return fetch('<?= $updateUrl ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Обновляем оригинальное содержимое
            element.setAttribute('data-original-content', content);
            changedElements.delete(element);
            
            // Показываем уведомление об успехе
            Swal.fire({
                title: 'Сохранено!',
                text: 'Изменения сохранены',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            throw new Error(data.message || 'Ошибка сохранения');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Ошибка!',
            text: error.message || 'Произошла ошибка при сохранении',
            icon: 'error',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    });
}

// Инициализация редактируемых элементов
document.querySelectorAll('.editable-content').forEach(element => {
    // Сохраняем исходное содержимое
    element.setAttribute('data-original-content', element.innerText.trim());
    
    // Добавляем обработчик изменений
    element.addEventListener('input', () => {
        changedElements.add(element);
    });

    // Сохраняем при потере фокуса
    element.addEventListener('blur', () => {
        const newContent = element.innerText.trim();
        if (newContent !== element.getAttribute('data-original-content')) {
            saveInlineContent(element, newContent);
        }
    });
});

// Функция для сохранения всех изменений
function saveAllChanges() {
    const promises = [];
    
    changedElements.forEach(element => {
        const content = element.innerText.trim();
        if (content !== element.getAttribute('data-original-content')) {
            promises.push(saveInlineContent(element, content));
        }
    });

    if (promises.length === 0) {
        Swal.fire({
            title: 'Информация',
            text: 'Нет несохраненных изменений',
            icon: 'info',
            confirmButtonText: 'OK'
        });
        return;
    }

    Promise.all(promises)
        .then(() => {
            if (changedElements.size === 0) {
                Swal.fire({
                    title: 'Успех!',
                    text: 'Все изменения сохранены',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Перенаправляем на страницу about после успешного сохранения
                    window.location.href = '<?= $aboutUrl ?>';
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Ошибка!',
                text: 'Произошла ошибка при сохранении изменений',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
}
</script>
