<?php

// Объявляем пространство имен для контроллера
namespace app\controllers;

// Подключаем необходимые классы и компоненты Yii
use Yii;
use app\models\PageContent;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

// Определяем класс контроллера для работы с контентом страниц
class PageContentController extends Controller
{
    // Метод для определения поведения контроллера
    public function behaviors()
    {
        // Возвращаем массив с правилами доступа
        return [
            // Правила доступа для контроллера
            'access' => [
                // Класс для управления доступом
                'class' => AccessControl::class,
                // Правила доступа
                'rules' => [
                    // Правило для доступа авторизованных пользователей
                    [
                        // Разрешаем доступ
                        'allow' => true,
                        // Роли, которым разрешен доступ
                        'roles' => ['@'],
                        // Callback-функция для проверки доступа
                        'matchCallback' => function ($rule, $action) {
                            // Проверяем, является ли пользователь администратором
                            return Yii::$app->user->identity->is_admin == 1 || Yii::$app->user->identity->is_admin == 2;
                        }
                    ],
                ],
            ],
        ];
    }

    // Метод для обновления контента страницы
    public function actionUpdate($id)
    {
        // Находим модель контента по идентификатору
        $model = $this->findModel($id);

        // Проверяем, загружены ли данные из запроса и сохранены ли изменения
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Очищаем кэш для этой страницы
            $cacheKey = "page_content_{$model->page_url}_{$model->block_identifier}";
            // Удаляем кэш по ключу
            Yii::$app->cache->delete($cacheKey);
            
            // Устанавливаем сообщение об успешном обновлении
            Yii::$app->session->setFlash('success', 'Контент успешно обновлен');
            // Перенаправляем пользователя на главную страницу
            return $this->redirect(['/site/index']);
        }

        // Рендерим вид для обновления контента
        return $this->render('update', [
            // Передаем модель контента в вид
            'model' => $model,
        ]);
    }

    // Защищенный метод для поиска модели контента по идентификатору
    protected function findModel($id)
    {
        // Находим модель контента по идентификатору
        if (($model = PageContent::findOne($id)) !== null) {
            // Возвращаем найденную модель
            return $model;
        }

        // Выбрасываем исключение, если модель не найдена
        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
