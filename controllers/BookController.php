<?php
// Объявление PHP кода

// Определение пространства имен для контроллера
namespace app\controllers;

// Импорт основного класса Yii
use Yii;
// Импорт исключения NotFoundHttpException для обработки ошибок, когда ресурс не найден
use yii\web\NotFoundHttpException;
// Импорт фильтра VerbFilter для ограничения HTTP методов
use yii\filters\VerbFilter;
// Импорт фильтра AccessControl для управления доступом
use yii\filters\AccessControl;
// Импорт исключения ForbiddenHttpException для обработки ошибок доступа
use yii\web\ForbiddenHttpException;
// Импорт класса Pagination для работы с пагинацией
use yii\data\Pagination;
// Импорт модели Book
use app\models\Book;
// Импорт модели Category
use app\models\Category;
// Импорт модели Comment
use app\models\Comment;
// Импорт модели Favorite
use app\models\Favorite;
// Импорт модели ViewHistory
use app\models\ViewHistory;
// Импорт модели BookSearch для поиска книг
use app\models\BookSearch;
// Импорт модели SeoSettings для SEO настроек
use app\models\SeoSettings;
// Импорт класса Response для работы с HTTP ответами
use yii\web\Response;
// Импорт базового контроллера
use app\controllers\BaseController;

/**
 * Контроллер для управления книгами
 * Предоставляет функции для просмотра, поиска и управления книгами
 */
class BookController extends BaseController
{
    /**
     * Настройка поведения контроллера
     * - Доступ только для авторизованных пользователей
     * - Ограничение HTTP методов для некоторых действий
     */
    public function behaviors()
    {
        // Возвращает массив конфигураций поведения
        return [
            'access' => [
                'class' => AccessControl::className(), // Класс контроля доступа
                'only' => ['books', 'view'], // Действия, для которых применяется фильтр
                'rules' => [
                    [
                        'actions' => ['books', 'view'], // Действия, разрешенные для доступа
                        'allow' => true, // Разрешить доступ
                        'roles' => ['@'], // Только для авторизованных пользователей
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    // Сообщение об ошибке и перенаправление на страницу входа при отказе в доступе
                    Yii::$app->session->setFlash('error', 'Для просмотра библиотеки необходимо войти в систему.');
                    return $this->redirect(['/site/login']);
                }
            ],
            'verbs' => [
                'class' => VerbFilter::className(), // Класс фильтрации HTTP методов
                'actions' => [
                    'delete' => ['post'], // Ограничение метода DELETE только для POST запросов
                ],
            ],
        ];
    }

    /**
     * Определение дополнительных действий контроллера
     * @return array - массив дополнительных действий
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction', // Действие для обработки ошибок
            ],
        ];
    }

    /**
     * Метод, выполняющийся перед любым действием контроллера
     * @param \yii\base\Action $action - действие, которое будет выполнено
     * @return bool - результат выполнения метода
     */
    public function beforeAction($action)
    {
        // Выполняем стандартную проверку перед действием
        if (!parent::beforeAction($action)) {
            return false; // Если проверка не пройдена, возвращаем false
        }

        return true; // Возвращаем true, если все проверки пройдены
    }

    /**
     * Отображение списка книг
     * @return string - HTML контент страницы
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch(); // Создание модели поиска книг
        // Получаем параметры поиска и фильтрации
        $search = Yii::$app->request->get('search');
        $categoryId = Yii::$app->request->get('category');
        
        // Формируем базовый запрос
        $query = Book::find()
            ->with(['category', 'comments']) // Подгружаем связанные данные
            ->orderBy(['created_at' => SORT_DESC]); // Сортировка по дате создания

        // Применяем фильтры если они есть
        if ($search) {
            $query->andWhere(['or',
                ['like', 'title', $search],
                ['like', 'author_firstname', $search],
                ['like', 'author_lastname', $search]
            ]);
        }

        if ($categoryId) {
            $query->andWhere(['category_id' => $categoryId]);
        }

        // Настройка пагинации
        $countQuery = clone $query;
        $pages = new \yii\data\Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 12
        ]);

        $books = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('books', [
            'searchModel' => $searchModel,
            'books' => $books,
            'categories' => Category::find()->all(),
            'selectedCategory' => $categoryId,
            'searchQuery' => $search,
            'pagination' => $pages
        ]);
    }

    /**
     * Отображение книг с фильтрацией и пагинацией
     * @return string - HTML контент страницы
     */
    public function actionBooks()
    {
        $query = Book::find(); // Создание запроса для получения всех книг
        
        // Показываем только не скрытые книги
        $query->andWhere(['is_hidden' => false]);
        
        // Получаем параметры фильтрации
        $category_id = Yii::$app->request->get('category_id');
        $searchQuery = Yii::$app->request->get('q');
        
        // Применяем фильтры к запросу
        if (!empty($category_id)) {
            $query->andWhere(['category_id' => $category_id]);
        }
        
        if (!empty($searchQuery)) {
            $searchQuery = trim($searchQuery);
            $query->andWhere([
                'or',
                ['like', 'title', $searchQuery],
                ['like', 'author_firstname', $searchQuery],
                ['like', 'author_lastname', $searchQuery]
            ]);
        }

        // Создаем пагинацию
        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 12,
        ]);

        // Получаем книги для текущей страницы
        $books = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        // Получаем все категории для фильтра
        $categories = Category::find()->all();

        return $this->render('books', [
            'books' => $books,
            'categories' => $categories,
            'selectedCategory' => $category_id,
            'searchQuery' => $searchQuery,
            'pagination' => $pages,
        ]);
    }

    /**
     * Добавление комментария к книге
     * @param int $id - ID книги
     * @return \yii\web\Response - объект ответа
     */
    public function actionAddComment($id)
    {
        // Проверяем, авторизован ли пользователь
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/site/login']); // Перенаправляем на страницу входа
        }

        $book = Book::findOne($id); // Поиск книги по ID
        if (!$book) {
            throw new NotFoundHttpException('Книга не найдена.'); // Если книга не найдена, выбрасываем исключение
        }

        $comment = new Comment(); // Создание новой модели комментария
        $comment->book_id = $id; // Установка ID книги
        $comment->user_id = Yii::$app->user->id; // Установка ID пользователя
        $comment->text = Yii::$app->request->post('comment'); // Получение текста комментария из POST данных
        // created_at будет установлен автоматически базой данных

        // Сохранение комментария и установка сообщения об успехе или ошибке
        if ($comment->save()) {
            Yii::$app->session->setFlash('success', 'Комментарий добавлен.');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при добавлении комментария: ' . implode(', ', $comment->getErrorSummary(true)));
        }

        return $this->redirect(['/book/view', 'id' => $id]); // Перенаправление на страницу книги
    }

    /**
     * Просмотр книги
     * @param string $id ID книги или SEO URL
     * @return string
     * @throws NotFoundHttpException если книга не найдена
     */
    public function actionView($id)
    {
        // Поиск книги по SEO URL или ID
        $model = Book::find()
            ->where(['OR', ['id' => $id], ['seo_url' => $id]])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Книга не найдена.');
        }

        // Если запрос пришел по ID, но есть SEO URL - делаем редирект
        if (is_numeric($id)) {
            // Если SEO URL пустой, генерируем его
            if (empty($model->seo_url)) {
                $model->seo_url = $model->generateSeoUrl($model->title);
                $model->save(false);
            }
            return $this->redirect(['/book/view', 'id' => $model->seo_url], 301);
        }

        // Увеличиваем счетчик просмотров
        $model->updateCounters(['views' => 1]);

        // Проверяем, не скрыта ли книга
        if ($model->is_hidden && !Yii::$app->user->can('admin')) {
            throw new \yii\web\NotFoundHttpException('Книга не найдена.'); // Если книга скрыта и пользователь не админ, выбрасываем исключение
        }

        // Добавляем просмотр только если пользователь авторизован
        if (!Yii::$app->user->isGuest) {
            // Добавляем уникальный просмотр
            $model->addView(Yii::$app->user->id);

            // Добавляем запись в историю просмотров
            $viewHistory = ViewHistory::find()
                ->where(['user_id' => Yii::$app->user->id, 'book_id' => $id])
                ->one();
            
            if ($viewHistory) {
                // Если запись существует, обновляем дату просмотра
                $viewHistory->viewed_at = new \yii\db\Expression('NOW()');
                $viewHistory->save();
            } else {
                // Если записи нет, создаем новую
                $viewHistory = new ViewHistory([
                    'user_id' => Yii::$app->user->id,
                    'book_id' => $id,
                    'viewed_at' => new \yii\db\Expression('NOW()')
                ]);
                $viewHistory->save();
            }
        }

        // Получаем комментарии к книге
        $comments = Comment::find()
            ->where(['book_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->with('user')
            ->all();

        return $this->render('view', [
            'model' => $model,
            'comments' => $comments
        ]);
    }

    /**
     * Проверяет, какие книги находятся в избранном у пользователя
     */
    public function actionCheckFavorites()
    {
        if (Yii::$app->user->isGuest) {
            return $this->asJson(['favorites' => []]);
        }

        $favorites = Yii::$app->db->createCommand('
            SELECT book_id FROM favorites WHERE user_id = :user_id
        ', [':user_id' => Yii::$app->user->id])->queryColumn();

        return $this->asJson(['favorites' => $favorites]);
    }

    /**
     * Добавление/удаление книги из избранного
     * Обрабатывает AJAX запрос
     * @return array JSON ответ
     */
    public function actionToggleFavorite()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Проверка авторизации
        if (Yii::$app->user->isGuest) {
            return [
                'success' => false,
                'message' => 'Необходимо авторизоваться'
            ];
        }

        $book_id = Yii::$app->request->post('id');
        if (empty($book_id)) {
            return [
                'success' => false,
                'message' => 'Не указан ID книги'
            ];
        }

        try {
            // Проверяем есть ли уже книга в избранном
            $favorite = Favorite::findOne([
                'user_id' => Yii::$app->user->id,
                'book_id' => $book_id
            ]);

            if ($favorite) {
                // Если есть - удаляем
                $favorite->delete();
                return [
                    'success' => true,
                    'message' => 'Книга удалена из избранного',
                    'is_favorite' => false
                ];
            } else {
                // Если нет - добавляем
                $favorite = new Favorite();
                $favorite->user_id = Yii::$app->user->id;
                $favorite->book_id = $book_id;
                $favorite->save();
                
                return [
                    'success' => true,
                    'message' => 'Книга добавлена в избранное',
                    'is_favorite' => true
                ];
            }
        } catch (\Exception $e) {
            Yii::error('Ошибка при работе с избранным: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Произошла ошибка при обработке запроса'
            ];
        }
    }

    /**
     * Удаление комментария
     * @param int $id ID комментария
     */
    public function actionDeleteComment($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $comment = Comment::findOne($id);
        if (!$comment) {
            throw new NotFoundHttpException('Комментарий не найден.');
        }

        // Проверяем права на удаление (админ или автор комментария)
        if (Yii::$app->user->identity->is_admin || $comment->user_id === Yii::$app->user->id) {
            $bookId = $comment->book_id;
            $comment->delete();
            Yii::$app->session->setFlash('success', 'Комментарий удален.');
            return $this->redirect(['view', 'id' => $bookId]);
        }

        throw new ForbiddenHttpException('У вас нет прав для удаления этого комментария.');
    }

    public function actionSearch()
    {
        return $this->render('search');
    }

    /**
     * Отображение всех избранных книг
     */
    public function actionFavorites()
    {
        $user = Yii::$app->user->identity;
        
        $favorites = Book::find()
            ->joinWith(['favorites f'])
            ->where(['f.user_id' => $user->id])
            ->orderBy(['f.created_at' => SORT_DESC])
            ->all();

        return $this->render('//site/favorites', [
            'books' => $favorites,
        ]);
    }

    /**
     * Отображение всей истории просмотров
     */
    public function actionHistory()
    {
        $user = Yii::$app->user->identity;
        
        $history = Book::find()
            ->joinWith(['viewHistory vh'])
            ->where(['vh.user_id' => $user->id])
            ->orderBy(['vh.viewed_at' => SORT_DESC])
            ->all();

        return $this->render('//site/history', [
            'books' => $history,
        ]);
    }

    /**
     * Отображение PDF файла книги
     * @param int $id ID книги
     * @return mixed
     * @throws NotFoundHttpException если книга не найдена
     */
    public function actionViewPdf($id)
    {
        $model = $this->findModel($id);
        
        if ($model->pdf_file && file_exists(Yii::getAlias('@webroot/uploads/pdfs/') . $model->pdf_file)) {
            return Yii::$app->response->sendFile(
                Yii::getAlias('@webroot/uploads/pdfs/') . $model->pdf_file,
                $model->title . '.pdf',
                ['inline' => true]
            );
        }
        
        throw new NotFoundHttpException('PDF файл не найден.');
    }

    /**
     * Отображение PDF файла книги
     * @param int $id ID книги
     * @return mixed
     * @throws NotFoundHttpException если книга не найдена
     */
    public function actionReadPdf($id)
    {
        $model = $this->findModel($id);
        
        if ($model->pdf_file && file_exists(Yii::getAlias('@webroot/uploads/pdfs/') . $model->pdf_file)) {
            // Увеличиваем счетчик просмотров
            $model->updateCounters(['views' => 1]);
            
            // Отправляем файл для просмотра в браузере
            return Yii::$app->response->sendFile(
                Yii::getAlias('@webroot/uploads/pdfs/') . $model->pdf_file,
                $model->pdf_file,
                ['inline' => true]
            );
        }
        
        throw new NotFoundHttpException('PDF файл не найден.');
    }

    /**
     * Загружает дополнительные комментарии для книги
     * @param integer $id ID книги
     * @param integer $offset Смещение для загрузки комментариев
     * @return string JSON-ответ с HTML-кодом комментариев
     */
    public function actionLoadComments($id, $offset = 0)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $limit = 4; // Количество комментариев для загрузки
        
        $comments = Comment::find()
            ->where(['book_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($limit + 1) // Загружаем на 1 больше, чтобы проверить, есть ли ещё комментарии
            ->with('user')
            ->all();
            
        $hasMore = count($comments) > $limit;
        if ($hasMore) {
            array_pop($comments); // Удаляем последний комментарий, он нужен был только для проверки
        }
        
        $html = '';
        foreach ($comments as $comment) {
            $html .= $this->renderPartial('_comment', [
                'comment' => $comment
            ]);
        }
        
        return [
            'success' => true,
            'html' => $html,
            'hasMore' => $hasMore,
            'nextOffset' => $offset + $limit
        ];
    }

    /**
     * Поиск модели книги по ID
     * @param int $id - ID книги
     * @return Book - модель книги
     * @throws NotFoundHttpException - если книга не найдена
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model; // Возвращаем найденную модель
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.'); // Если книга не найдена, выбрасываем исключение
    }
}
