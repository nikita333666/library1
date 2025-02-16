<?php

// Объявляем пространство имен для контроллера
namespace app\controllers;

// Импортируем класс Yii
use Yii;
// Импортируем базовый класс Controller
use yii\web\Controller;
// Импортируем модель PageContent
use app\models\PageContent;
// Импортируем модель AboutContent
use app\models\AboutContent;
// Импортируем класс для управления доступом
use yii\filters\AccessControl;
// Импортируем класс для работы с HTTP ответами
use yii\web\Response;

// Определяем класс контроллера для редактирования контента страниц
class PageEditorController extends Controller
{
    // Метод для определения поведения контроллера
    public function behaviors()
    {
        // Возвращаем массив с правилами доступа
        return [
            // Определяем правила доступа для контроллера
            'access' => [
                // Указываем класс для управления доступом
                'class' => AccessControl::class,
                // Определяем правила доступа
                'rules' => [
                    [
                        // Разрешаем доступ
                        'allow' => true,
                        // Указываем роли, которым разрешен доступ
                        'roles' => ['@'],
                        // Определяем callback-функцию для проверки доступа
                        'matchCallback' => function ($rule, $action) {
                            // Проверяем, является ли пользователь администратором
                            return Yii::$app->user->identity->is_admin == 1 || Yii::$app->user->identity->is_admin == 2;
                        }
                    ],
                ],
            ],
        ];
    }

    // Метод для отображения редактируемых блоков контента главной страницы
    public function actionIndex()
    {
        // Инициализируем массив для хранения контента главной страницы
        $pageContent = [
            // Получаем заголовок приветствия
            'welcomeTitle' => PageContent::findOne(['page_url' => 'site/index', 'block_identifier' => 'welcome_title']),
            // Получаем подзаголовок приветствия
            'welcomeSubtitle' => PageContent::findOne(['page_url' => 'site/index', 'block_identifier' => 'welcome_subtitle']),
            // Получаем заголовок первой особенности
            'feature1Title' => PageContent::findOne(['page_url' => 'site/index', 'block_identifier' => 'feature_1_title']),
            // Получаем текст первой особенности
            'feature1Text' => PageContent::findOne(['page_url' => 'site/index', 'block_identifier' => 'feature_1_text']),
            // Получаем заголовок второй особенности
            'feature2Title' => PageContent::findOne(['page_url' => 'site/index', 'block_identifier' => 'feature_2_title']),
            // Получаем текст второй особенности
            'feature2Text' => PageContent::findOne(['page_url' => 'site/index', 'block_identifier' => 'feature_2_text']),
            // Получаем заголовок третьей особенности
            'feature3Title' => PageContent::findOne(['page_url' => 'site/index', 'block_identifier' => 'feature_3_title']),
            // Получаем текст третьей особенности
            'feature3Text' => PageContent::findOne(['page_url' => 'site/index', 'block_identifier' => 'feature_3_text']),
        ];

        // Возвращаем представление с данными контента
        return $this->render('index', $pageContent);
    }

    // Метод для отображения контента страницы "О нас"
    public function actionAbout()
    {
        // Определяем массив секций страницы "О нас"
        $sections = ['hero', 'welcome', 'genres', 'features', 'stats'];
        // Инициализируем массив для хранения данных контента
        $data = [];
        
        // Получаем заголовок секции "Герой"
        $data['heroTitle'] = AboutContent::findOne(['section' => 'hero', 'identifier' => 'title']);
        // Получаем подзаголовок секции "Герой"
        $data['heroSubtitle'] = AboutContent::findOne(['section' => 'hero', 'identifier' => 'subtitle']);
        
        // Получаем заголовок секции "Добро пожаловать"
        $data['welcomeTitle'] = AboutContent::findOne(['section' => 'welcome', 'identifier' => 'title']);
        // Получаем текст секции "Добро пожаловать"
        $data['welcomeText'] = AboutContent::findOne(['section' => 'welcome', 'identifier' => 'text']);
        
        // Получаем заголовок секции "Жанры"
        $data['genresTitle'] = AboutContent::findOne(['section' => 'genres', 'identifier' => 'title']);
        // Цикл для получения данных о жанрах
        for ($i = 0; $i < 6; $i++) {
            // Получаем заголовок жанра
            $data['genreTitle'.$i] = AboutContent::findOne(['section' => 'genres', 'identifier' => 'title_'.$i]);
            // Получаем описание жанра
            $data['genreDesc'.$i] = AboutContent::findOne(['section' => 'genres', 'identifier' => 'desc_'.$i]);
        }
        
        // Получаем заголовок секции "Особенности"
        $data['featuresTitle'] = AboutContent::findOne(['section' => 'features', 'identifier' => 'title']);
        // Цикл для получения данных об особенностях
        for ($i = 0; $i < 3; $i++) {
            // Получаем заголовок особенности
            $data['featureTitle'.$i] = AboutContent::findOne(['section' => 'features', 'identifier' => 'title_'.$i]);
            // Получаем описание особенности
            $data['featureDesc'.$i] = AboutContent::findOne(['section' => 'features', 'identifier' => 'desc_'.$i]);
        }
        
        // Получаем заголовок секции "Статистика"
        $data['statsTitle'] = AboutContent::findOne(['section' => 'stats', 'identifier' => 'title']);
        // Цикл для получения данных о статистике
        for ($i = 0; $i < 4; $i++) {
            // Получаем числовое значение статистики
            $data['statNumber'.$i] = AboutContent::findOne(['section' => 'stats', 'identifier' => 'number_'.$i]);
            // Получаем описание статистики
            $data['statLabel'.$i] = AboutContent::findOne(['section' => 'stats', 'identifier' => 'label_'.$i]);
        }
        
        // Возвращаем представление с данными контента
        return $this->render('about', $data);
    }

    // Метод для обновления контента страницы
    public function actionUpdateContent()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        try {
            $section = Yii::$app->request->post('section', 'main'); // Добавляем значение по умолчанию
            $identifier = Yii::$app->request->post('identifier');
            $content = Yii::$app->request->post('content');
            $page = Yii::$app->request->post('page');
            
            if ($page === 'site/about') {
                $model = AboutContent::findOne(['section' => $section, 'identifier' => $identifier]);
                if (!$model) {
                    $model = new AboutContent();
                    $model->section = $section;
                    $model->identifier = $identifier;
                }
            } else {
                $model = PageContent::findOne([
                    'page_url' => $page,
                    'block_identifier' => $identifier
                ]);
                
                if (!$model) {
                    $model = new PageContent();
                    $model->page_url = $page;
                    $model->block_identifier = $identifier;
                }
            }
            
            $model->content = $content;
            
            if ($model->save()) {
                // Очищаем кэш для всех страниц
                if ($page === 'site/index') {
                    // Очищаем кэш для всех блоков главной страницы
                    $cacheKeys = [
                        "page_content_{$page}_welcome_title",
                        "page_content_{$page}_welcome_subtitle",
                        "page_content_{$page}_feature_1_title",
                        "page_content_{$page}_feature_1_text",
                        "page_content_{$page}_feature_2_title",
                        "page_content_{$page}_feature_2_text",
                        "page_content_{$page}_feature_3_title",
                        "page_content_{$page}_feature_3_text"
                    ];
                    foreach ($cacheKeys as $key) {
                        Yii::$app->cache->delete($key);
                    }
                    // Очищаем общий кэш главной страницы
                    Yii::$app->cache->delete('home_page_content');
                } elseif ($page !== 'site/about') {
                    $cacheKey = "page_content_{$model->page_url}_{$model->block_identifier}";
                    Yii::$app->cache->delete($cacheKey);
                }
                
                return [
                    'success' => true,
                    'message' => 'Контент успешно обновлен'
                ];
            } else {
                Yii::error('Failed to save content: ' . json_encode($model->errors));
                return [
                    'success' => false,
                    'error' => 'Не удалось сохранить контент',
                    'details' => $model->errors
                ];
            }
        } catch (\Exception $e) {
            Yii::error('Error updating content: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Произошла ошибка при обновлении контента',
                'details' => $e->getMessage()
            ];
        }
    }
}
