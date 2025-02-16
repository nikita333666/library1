<?php
// Объявление PHP кода

// Определение пространства имен для контроллера
namespace app\controllers;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса Controller
use yii\web\Controller;
// Импорт исключения NotFoundHttpException для обработки ошибок, когда ресурс не найден
use yii\web\NotFoundHttpException;
// Импорт фильтра VerbFilter для ограничения HTTP методов
use yii\filters\VerbFilter;
// Импорт фильтра AccessControl для управления доступом
use yii\filters\AccessControl;
// Импорт модели Comment
use app\models\Comment;
// Импорт класса Response для работы с HTTP ответами
use yii\web\Response;

/**
 * Контроллер для управления комментариями
 * Добавление, редактирование и удаление комментариев к книгам
 */
class CommentController extends Controller
{
    /**
     * Настройка поведения контроллера
     * - Доступ только для авторизованных пользователей
     * - Проверка CSRF для POST запросов
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
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class, // Класс фильтрации HTTP методов
                'actions' => [
                    'delete' => ['POST'], // Ограничение метода DELETE только для POST запросов
                ],
            ],
        ];
    }

    /**
     * Удаление комментария
     * @param int $id - ID комментария
     * @return \yii\web\Response - объект ответа
     */
    public function actionDelete($id)
    {
        // Поиск модели комментария по ID
        $model = $this->findModel($id);
        
        // Проверяем права на удаление
        if (Yii::$app->user->identity->isAdmin() || Yii::$app->user->id === $model->user_id) {
            // Получаем ID книги
            $bookId = $model->book_id;
            // Удаляем комментарий
            $model->delete();
            // Устанавливаем сообщение об успехе
            Yii::$app->session->setFlash('success', 'Комментарий удален');
            // Перенаправляем на страницу книги
            return $this->redirect(['book/view', 'id' => $bookId]);
        }
        
        // Если нет прав, выбрасываем исключение
        throw new \yii\web\ForbiddenHttpException('У вас нет прав для выполнения этого действия');
    }

    /**
     * Поиск комментария по ID
     * @param int $id - ID комментария
     * @return Comment - модель комментария
     * @throws NotFoundHttpException - если комментарий не найден
     */
    protected function findModel($id)
    {
        // Проверяем, существует ли комментарий с указанным ID
        if (($model = Comment::findOne($id)) !== null) {
            // Возвращаем найденную модель
            return $model;
        }

        // Если комментарий не найден, выбрасываем исключение
        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
