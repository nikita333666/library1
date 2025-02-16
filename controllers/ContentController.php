<?php
// Объявление PHP кода

// Определение пространства имен для контроллера
namespace app\controllers;

// Импорт основного класса Yii
use Yii; // Основной класс Yii
// Импорт модели PageContent
use app\models\PageContent; // Модель контента страницы
// Импорт базового класса Controller
use yii\web\Controller; // Базовый класс контроллера
// Импорт исключения NotFoundHttpException для обработки ошибок, когда ресурс не найден
use yii\web\NotFoundHttpException; // Исключение для не найденного ресурса
// Импорт фильтра AccessControl для управления доступом
use yii\filters\AccessControl; // Фильтр для управления доступом

/**
 * Контроллер для управления контентом страниц
 * Предоставляет функции для просмотра, обновления и создания контента страниц
 */
class ContentController extends Controller
{
    /**
     * Настройка поведения контроллера
     * - Доступ только для администраторов
     */
    public function behaviors()
    {
        // Возвращает массив конфигураций поведения
        return [
            'access' => [
                'class' => AccessControl::class, // Класс контроля доступа
                'rules' => [
                    [
                        'allow' => true, // Разрешить доступ
                        'roles' => ['@'], // Только для авторизованных пользователей
                        'matchCallback' => function ($rule, $action) {
                            // Проверка, что пользователь является администратором
                            return Yii::$app->user->identity->is_admin == 1 || Yii::$app->user->identity->is_admin == 2;
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Отображение всех блоков контента
     * @return string - HTML контент страницы
     */
    public function actionIndex()
    {
        // Получение всех блоков контента
        $content = PageContent::find()->all(); 
        // Возвращаем представление с данными
        return $this->render('index', [
            'content' => $content, // Передача данных в представление
        ]);
    }

    /**
     * Обновление контента страницы
     * @param int $id - ID блока контента
     * @return string - HTML контент страницы
     */
    public function actionUpdate($id)
    {
        // Поиск модели контента по ID
        $model = $this->findModel($id); 

        // Загрузка данных из формы и сохранение изменений
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Очищаем кэш для этой страницы
            $cacheKey = "page_content_{$model->page_url}_{$model->block_identifier}";
            Yii::$app->cache->delete($cacheKey);
            
            // Устанавливаем сообщение об успехе
            Yii::$app->session->setFlash('success', 'Контент успешно обновлен');
            
            // Редиректим на ту страницу, контент которой редактировали
            $route = $model->page_url;
            return $this->redirect([$route]);
        }

        // Возвращаем представление для редактирования
        return $this->render('update', [
            'model' => $model, // Передача данных в представление
        ]);
    }

    /**
     * Создание нового блока контента
     * @return string - HTML контент страницы
     */
    public function actionCreate()
    {
        // Создание новой модели контента
        $model = new PageContent(); 

        // Загрузка данных из формы и сохранение нового блока контента
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Устанавливаем сообщение об успехе
            Yii::$app->session->setFlash('success', 'Контент успешно создан');
            // Перенаправление на страницу со списком контента
            return $this->redirect(['index']); 
        }

        // Возвращаем представление для создания
        return $this->render('create', [
            'model' => $model, // Передача данных в представление
        ]);
    }

    /**
     * Поиск модели контента по ID
     * @param int $id - ID блока контента
     * @return PageContent - модель контента
     * @throws NotFoundHttpException - если контент не найден
     */
    protected function findModel($id)
    {
        // Поиск модели контента по ID
        if (($model = PageContent::findOne($id)) !== null) {
            // Возвращаем найденную модель
            return $model; 
        }

        // Если контент не найден, выбрасываем исключение
        throw new NotFoundHttpException('Запрашиваемая страница не существует.'); 
    }
}
