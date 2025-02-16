<?php
// Подключаем необходимые классы Yii2 для работы с HTML и URL
use yii\helpers\Html;
use yii\helpers\Url;

// Исправляем URL для обновления контента
$updateUrl = Url::to(['page-editor/update-content']);
$homeUrl = Url::home();
$currentPage = 'site/index'; // Явно указываем, что это главная страница

// Устанавливаем заголовок страницы
$this->title = 'Редактор страницы';
// Подключаем CSS файл для стилизации книг
$this->registerCssFile('@web/css/books.css');

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

    <!-- Основной контейнер сайта -->
    <div class="site-container">
        <!-- Приветственная секция -->
        <div class="welcome-section">
            <!-- Блок с редактируемым заголовком -->
            <div class="editable-block">
                <h1 class="display-4 editable-content" data-identifier="welcome_title" contenteditable="true">
                    <?= Html::encode($welcomeTitle ? $welcomeTitle->content : 'Добро пожаловать в нашу библиотеку!') ?>
                </h1>
            </div>

            <!-- Блок с редактируемым подзаголовком -->
            <div class="editable-block">
                <p class="lead editable-content" data-identifier="welcome_subtitle" contenteditable="true">
                    <?= Html::encode($welcomeSubtitle ? $welcomeSubtitle->content : 'Откройте для себя мир книг') ?>
                </p>
            </div>
            
            <!-- Сетка с преимуществами -->
            <div class="features-grid">
                <!-- Первая карточка преимущества -->
                <div class="feature-item">
                    <!-- Иконка для первой карточки -->
                    <i class="fas fa-book-reader"></i>
                    <!-- Блок с редактируемым заголовком -->
                    <div class="editable-block">
                        <h3 class="editable-content" data-identifier="feature_1_title" contenteditable="true">
                            <?= Html::encode($feature1Title ? $feature1Title->content : 'Безграничный доступ') ?>
                        </h3>
                    </div>
                    <!-- Блок с редактируемым текстом -->
                    <div class="editable-block">
                        <p class="editable-content" data-identifier="feature_1_text" contenteditable="true">
                            <?= Html::encode($feature1Text ? $feature1Text->content : 'Более 1000 книг различных жанров в вашем распоряжении') ?>
                        </p>
                    </div>
                </div>

                <!-- Вторая карточка преимущества -->
                <div class="feature-item">
                    <!-- Иконка для второй карточки -->
                    <i class="fas fa-mobile-alt"></i>
                    <!-- Блок с редактируемым заголовком -->
                    <div class="editable-block">
                        <h3 class="editable-content" data-identifier="feature_2_title" contenteditable="true">
                            <?= Html::encode($feature2Title ? $feature2Title->content : 'Читайте где угодно') ?>
                        </h3>
                    </div>
                    <!-- Блок с редактируемым текстом -->
                    <div class="editable-block">
                        <p class="editable-content" data-identifier="feature_2_text" contenteditable="true">
                            <?= Html::encode($feature2Text ? $feature2Text->content : 'Доступ к библиотеке с любого устройства 24/7') ?>
                        </p>
                    </div>
                </div>

                <!-- Третья карточка преимущества -->
                <div class="feature-item">
                    <!-- Иконка для третьей карточки -->
                    <i class="fas fa-bookmark"></i>
                    <!-- Блок с редактируемым заголовком -->
                    <div class="editable-block">
                        <h3 class="editable-content" data-identifier="feature_3_title" contenteditable="true">
                            <?= Html::encode($feature3Title ? $feature3Title->content : 'Бесплатно') ?>
                        </h3>
                    </div>
                    <!-- Блок с редактируемым текстом -->
                    <div class="editable-block">
                        <p class="editable-content" data-identifier="feature_3_text" contenteditable="true">
                            <?= Html::encode($feature3Text ? $feature3Text->content : 'Все книги доступны бесплатно после регистрации') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS стили для редактора страницы -->
<style>
/* Основной контейнер редактора */
.page-editor {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Стили для приветственной секции */
.welcome-section {
    text-align: center;
    padding: 40px 20px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 40px;
}

/* Сетка для отображения преимуществ */
.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-top: 40px;
    padding: 0 20px;
}

/* Стили для карточки преимущества */
.feature-item {
    text-align: center;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

/* Эффект при наведении на карточку */
.feature-item:hover {
    transform: translateY(-5px);
}

/* Стили для иконок в карточках */
.feature-item i {
    font-size: 2.5em;
    color: #0d6efd;
    margin-bottom: 15px;
}

/* Стили для заголовков в карточках */
.feature-item h3 {
    font-size: 1.5em;
    margin-bottom: 10px;
    color: #333;
}

/* Стили для текста в карточках */
.feature-item p {
    color: #666;
    line-height: 1.6;
}

/* Стили для редактируемого контента */
.editable-content {
    padding: 5px;
    border-radius: 4px;
    min-height: 20px;
    transition: all 0.3s ease;
    cursor: text;
}

/* Эффект при наведении на редактируемый контент */
.editable-content:hover {
    background-color: rgba(0,123,255,0.1);
}

/* Стили при фокусе на редактируемом контенте */
.editable-content:focus {
    outline: none;
    background: white;
    border: 1px solid #007bff;
}

/* Адаптивная верстка для мобильных устройств */
@media (max-width: 768px) {
    .features-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
// Добавляем переменные для JavaScript
$csrfParam = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;
?>

<script>
const changedElements = new Set();

function showNotification(type, message) {
    Swal.fire({
        icon: type,
        title: type === 'success' ? 'Успешно!' : 'Ошибка!',
        text: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
}

function saveInlineContent(element, content) {
    const identifier = element.getAttribute('data-identifier');
    
    fetch('<?= $updateUrl ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            '<?= $csrfParam ?>': '<?= $csrfToken ?>'
        },
        body: new URLSearchParams({
            identifier: identifier,
            content: content,
            page: '<?= $currentPage ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            element.setAttribute('data-original-content', content);
            showNotification('success', 'Изменения сохранены');
        } else {
            showNotification('error', data.error || 'Ошибка при сохранении');
            console.error('Error:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Ошибка при сохранении');
    });
}

document.querySelectorAll('.editable-content').forEach(element => {
    element.setAttribute('data-original-content', element.innerText.trim());
});

document.querySelectorAll('.editable-content').forEach(element => {
    element.addEventListener('input', () => {
        changedElements.add(element);
    });

    element.addEventListener('blur', () => {
        if (element.innerText.trim() !== element.getAttribute('data-original-content')) {
            saveInlineContent(element, element.innerText.trim());
        }
    });
});

function saveAllChanges() {
    const promises = [];
    
    changedElements.forEach(element => {
        const content = element.innerText.trim();
        if (content !== element.getAttribute('data-original-content')) {
            promises.push(
                new Promise((resolve) => {
                    saveInlineContent(element, content);
                    resolve();
                })
            );
        }
    });

    if (promises.length === 0) {
        window.location.href = '<?= $homeUrl ?>';
        return;
    }

    Promise.all(promises).then(() => {
        // Добавляем небольшую задержку перед редиректом
        setTimeout(() => {
            window.location.href = '<?= $homeUrl ?>';
        }, 500);
    });
}
</script>
