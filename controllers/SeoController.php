<?php

// Объявляем пространство имен для контроллера
namespace app\controllers;

// Импортируем класс Yii
use Yii;
// Импортируем модель SeoSettings
use app\models\SeoSettings;
// Импортируем базовый класс Controller
use yii\web\Controller;
// Импортируем исключение NotFoundHttpException для обработки ошибок, когда ресурс не найден
use yii\web\NotFoundHttpException;
// Импортируем класс для управления доступом
use yii\filters\AccessControl;
// Импортируем фильтр VerbFilter для ограничения HTTP методов
use yii\filters\VerbFilter;

// Определяем класс контроллера для управления SEO настройками
class SeoController extends Controller
{
    // Метод для определения поведения контроллера
    public function behaviors()
    {
        // Возвращаем массив с правилами доступа и методами
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
                            return Yii::$app->user->identity->is_admin;
                        }
                    ],
                ],
            ],
            // Определяем методы доступа
            'verbs' => [
                // Указываем класс для фильтрации HTTP методов
                'class' => VerbFilter::class,
                // Определяем методы для действий
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    // Метод для отображения и обновления SEO настроек
    public function actionIndex()
    {
        // Определяем маршруты страниц
        $routes = [
            'site/index' => 'Главная страница',
            'site/about' => 'О нас',
            'book/books' => 'Библиотека книг'
        ];

        // Проверяем и обновляем текущие значения SEO для каждой страницы
        foreach ($routes as $route => $name) {
            // Ищем настройки SEO по URL страницы
            $seoSettings = SeoSettings::findOne(['page_url' => $route]);
            // Если настройки не найдены, создаем новые
            if (!$seoSettings) {
                $seoSettings = new SeoSettings();
                $seoSettings->page_url = $route;
            }

            // Получаем текущие значения со страницы
            $pageContent = $this->getPageContent($route);
            // Если контент страницы получен
            if ($pageContent) {
                // Устанавливаем текущие значения заголовка и описания
                $seoSettings->current_title = $pageContent['title'];
                $seoSettings->current_description = $pageContent['description'];
                
                // Если это новая запись, используем текущие значения как значения по умолчанию
                if ($seoSettings->isNewRecord) {
                    $seoSettings->title = $pageContent['title'];
                    $seoSettings->description = $pageContent['description'];
                }
                
                // Сохраняем настройки SEO
                $seoSettings->save();
            }
        }

        // Получаем все SEO записи
        $seoSettings = SeoSettings::find()->all();

        // Возвращаем представление с настройками SEO
        return $this->render('index', [
            'seoSettings' => $seoSettings,
            'pages' => $routes,
        ]);
    }

    // Метод для обновления SEO настроек по идентификатору
    public function actionUpdate($id)
    {
        // Находим модель SEO настроек по идентификатору
        $model = $this->findModel($id);
        // Определяем маршруты страниц
        $routes = [
            'site/index' => 'Главная страница',
            'site/about' => 'О нас',
            'book/books' => 'Библиотека книг'
        ];

        // Если это GET запрос, обновляем текущие значения
        if (Yii::$app->request->isGet) {
            // Получаем текущие значения со страницы
            $pageContent = $this->getPageContent($model->page_url);
            // Если контент страницы получен
            if ($pageContent) {
                // Устанавливаем текущие значения заголовка и описания
                $model->current_title = $pageContent['title'];
                $model->current_description = $pageContent['description'];
                // Сохраняем модель
                $model->save();
            }
        }

        // Если данные загружены и модель сохранена
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Устанавливаем сообщение об успешном обновлении
            Yii::$app->session->setFlash('success', 'SEO мета-теги успешно обновлены');
            // Перенаправляем на страницу по URL
            return $this->redirect(['/' . $model->page_url]);
        }

        // Возвращаем представление для обновления SEO настроек
        return $this->render('update', [
            'model' => $model,
            'pages' => $routes,
        ]);
    }

    // Защищенный метод для поиска модели SEO настроек по идентификатору
    protected function findModel($id)
    {
        // Ищем модель SEO настроек по идентификатору
        if (($model = SeoSettings::findOne($id)) !== null) {
            // Возвращаем найденную модель
            return $model;
        }

        // Выбрасываем исключение, если модель не найдена
        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }

    // Приватный метод для получения контента страницы по маршруту
    private function getPageContent($route)
    {
        // Разбираем маршрут
        list($controllerId, $actionId) = explode('/', $route);
        
        // Создаем контроллер
        $controller = Yii::$app->createController($route)[0];
        // Если контроллер не создан, возвращаем null
        if (!$controller) {
            return null;
        }

        // Создаем новый view
        $view = $this->getView();
        // Устанавливаем контекст для view
        $view->context = $controller;

        try {
            // Рендерим страницу
            $controller->{'action' . ucfirst($actionId)}();
            
            // Получаем title и description
            return [
                'title' => $view->title,
                'description' => $view->params['metaDescription'] ?? ''
            ];
        } catch (\Exception $e) {
            // Логируем ошибку при получении контента страницы
            Yii::error("Ошибка при получении контента страницы {$route}: " . $e->getMessage());
            // Возвращаем null в случае ошибки
            return null;
        }
    }
}
