<?php

// Объявляем пространство имен для контроллера
namespace app\controllers;

// Импортируем класс Yii
use Yii;
// Импортируем класс для управления доступом
use yii\filters\AccessControl;
// Импортируем фильтр VerbFilter для ограничения HTTP методов
use yii\filters\VerbFilter;
// Импортируем класс Pagination для работы с пагинацией
use yii\data\Pagination;
// Импортируем класс Query для построения запросов
use yii\db\Query;
// Импортируем модель LoginForm для авторизации
use app\models\LoginForm;
// Импортируем модель Book для работы с книгами
use app\models\Book;
// Импортируем модель Category для работы с категориями
use app\models\Category;
// Импортируем модель Comment для работы с комментариями
use app\models\Comment;
// Импортируем модель User для работы с пользователями
use app\models\User;
// Импортируем модель Favorite для работы с избранным
use app\models\Favorite;
// Импортируем модель ViewHistory для работы с историей просмотров
use app\models\ViewHistory;
// Импортируем модель VerifyForm для верификации
use app\models\VerifyForm;
// Импортируем модель PageContent для работы с контентом страниц
use app\models\PageContent;
// Импортируем вспомогательный класс ArrayHelper
use yii\helpers\ArrayHelper;
// Импортируем класс UploadedFile для работы с загружаемыми файлами
use yii\web\UploadedFile;
// Импортируем исключение NotFoundHttpException для обработки ошибок, когда ресурс не найден
use yii\web\NotFoundHttpException;
// Импортируем модель ChangePasswordForm для изменения пароля
use app\models\ChangePasswordForm;
// Импортируем базовый контроллер
use app\controllers\BaseController;
// Импортируем класс для работы с HTTP ответами
use yii\web\Response;

/**
 * Основной контроллер сайта
 * Отвечает за главную страницу, авторизацию, регистрацию и профиль пользователя
 */
class SiteController extends BaseController
{
    /**
     * Настройка поведения контроллера
     * - Правила доступа для разных действий
     * - Настройка методов HTTP для действий
     */
    public function behaviors()
    {
        // Возвращаем массив с правилами доступа и методами
        return [
            // Определяем правила доступа для контроллера
            'access' => [
                // Указываем класс для управления доступом
                'class' => AccessControl::className(),
                // Указываем действия, для которых применяются правила
                'only' => ['logout', 'admin', 'update-content', 'index'],
                // Определяем правила доступа
                'rules' => [
                    [
                        // Разрешаем доступ к действию index
                        'actions' => ['index'],
                        // Разрешаем доступ
                        'allow' => true,
                        // Указываем роли, которым разрешен доступ
                        'roles' => ['@', '?'],
                    ],
                    [
                        // Разрешаем доступ к действию logout
                        'actions' => ['logout', 'update-content'],
                        // Разрешаем доступ
                        'allow' => true,
                        // Указываем роли, которым разрешен доступ
                        'roles' => ['@'],
                    ],
                    [
                        // Разрешаем доступ к действию admin
                        'actions' => ['admin'],
                        // Разрешаем доступ
                        'allow' => true,
                        // Указываем роли, которым разрешен доступ
                        'roles' => ['@'],
                        // Определяем callback-функцию для проверки доступа
                        'matchCallback' => function ($rule, $action) {
                            // Проверяем, является ли пользователь администратором
                            return Yii::$app->user->identity->isAdmin();
                        }
                    ],
                ],
            ],
            // Определяем методы доступа
            'verbs' => [
                // Указываем класс для фильтрации HTTP методов
                'class' => VerbFilter::className(),
                // Определяем методы для действий
                'actions' => [
                    'logout' => ['post'],
                    'update-content' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        // Возвращаем массив с действиями
        return [
            // Действие для обработки ошибок
            'error' => [
                'class' => 'yii\\web\\ErrorAction',
            ],
            // Действие для работы с капчей
            'captcha' => [
                'class' => 'yii\\captcha\\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Главная страница
     * Отображает популярные, рекомендуемые и новые книги
     */
    public function actionIndex()
    {
        // Получаем самые просматриваемые книги
        $topViewed = Book::find()
            ->where(['is_hidden' => false])
            ->joinWith('viewHistory')
            ->select(['books.*', 'COUNT(view_history.id) as views_count'])
            ->groupBy('books.id')
            ->orderBy(['views_count' => SORT_DESC])
            ->limit(4)
            ->all();
        
        // Получаем 4 случайные книги для рекомендаций
        $recommendedBooks = Book::find()
            ->where(['is_hidden' => false])
            ->orderBy(['RAND()' => SORT_DESC])
            ->limit(4)
            ->all();

        // Получаем новые поступления
        $newBooks = Book::find()
            ->where(['is_hidden' => false])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(4)
            ->all();

        // Возвращаем представление с данными книг
        return $this->render('index', [
            'topViewed' => $topViewed,
            'recommendedBooks' => $recommendedBooks,
            'newBooks' => $newBooks,
        ]);
    }

    /**
     * Страница о нас
     */
    public function actionAbout()
    {
        // Возвращаем представление страницы "О нас"
        return $this->render('about');
    }

    /**
     * Регистрация нового пользователя
     * После успешной регистрации перенаправляет на страницу входа
     */
    public function actionSignup()
    {
        // Если пользователь уже авторизован
        if (!Yii::$app->user->isGuest) {
            // Перенаправляем на главную страницу
            return $this->goHome();
        }

        // Создаем модель формы регистрации
        $model = new \app\models\SignupForm();
        // Если форма загружена и регистрация успешна
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            // Устанавливаем сообщение об успешной регистрации
            Yii::$app->session->setFlash('success', 'Спасибо за регистрацию. Теперь вы можете войти в систему.');
            // Перенаправляем на страницу входа
            return $this->redirect(['site/login']);
        }

        // Возвращаем представление с моделью формы регистрации
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Вход в систему
     */
    public function actionLogin()
    {
        // Если пользователь уже авторизован
        if (!Yii::$app->user->isGuest) {
            // Если пользователь администратор, перенаправляем в админку
            if (Yii::$app->user->identity->is_admin) {
                return $this->redirect(['/site/admin']);
            }
            // Иначе перенаправляем на главную страницу
            return $this->redirect(['/site/index']);
        }

        // Создаем модель формы входа
        $model = new LoginForm();
        // Если форма загружена и вход успешен
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // Если пользователь администратор, перенаправляем в админку
            if (Yii::$app->user->identity->is_admin) {
                return $this->redirect(['/site/admin']);
            } else {
                // Иначе перенаправляем на главную страницу
                return $this->redirect(['/site/index']);
            }
        }

        // Очищаем поле пароля
        $model->password = '';
        // Возвращаем представление с моделью формы входа
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Admin panel action.
     *
     * @return string
     */
    public function actionAdmin()
    {
        // Если пользователь не авторизован или не администратор
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin()) {
            // Перенаправляем на страницу входа
            return $this->redirect(['site/login']);
        }

        // Получаем базовую статистику
        $stats = [
            // Количество пользователей
            'users_count' => \app\models\User::find()->count(),
            // Количество книг
            'books_count' => \app\models\Book::find()->where(['is_hidden' => false])->count(),
            // Количество категорий
            'categories_count' => \app\models\Category::find()->count(),
            // Последние зарегистрированные пользователи (лимит: 5)
            'latest_users' => \app\models\User::find()
                ->orderBy(['registration_date' => SORT_DESC])
                ->limit(5)
                ->all(),
            // Последние добавленные книги (лимит: 5)
            'latest_books' => \app\models\Book::find()
                ->where(['is_hidden' => false])
                ->orderBy(['created_at' => SORT_DESC])
                ->limit(5)
                ->all(),
        ];

        // Возвращаем представление с данными статистики
        return $this->render('admin', [
            'stats' => $stats,
        ]);
    }

    /**
     * Страница профиля пользователя
     * Отображает избранные книги и историю просмотров
     */
    public function actionProfile()
    {
        // Если пользователь не авторизован
        if (Yii::$app->user->isGuest) {
            // Перенаправляем на страницу входа
            return $this->redirect(['site/login']);
        }

        // Находим пользователя по ID
        $user = User::findOne(Yii::$app->user->id);
        // Создаем модель формы изменения пароля
        $passwordForm = new ChangePasswordForm($user);
        
        // Получаем избранные книги пользователя (лимит: 3)
        $favoriteBooks = Book::find()
            ->where(['is_hidden' => false])
            ->joinWith('favorites')
            ->where(['favorites.user_id' => Yii::$app->user->id])
            ->limit(3)
            ->all();

        // Получаем историю просмотров пользователя (лимит: 3)
        $historyBooks = Book::find()
            ->where(['is_hidden' => false])
            ->joinWith('viewHistory')
            ->where(['view_history.user_id' => Yii::$app->user->id])
            ->orderBy(['view_history.viewed_at' => SORT_DESC])
            ->limit(3)
            ->all();

        // Возвращаем представление с данными профиля
        return $this->render('profile', [
            'user' => $user,
            'passwordForm' => $passwordForm,
            'favoriteBooks' => $favoriteBooks,
            'historyBooks' => $historyBooks,
        ]);
    }

    /**
     * Страница всех избранных книг пользователя
     * Отображает список с пагинацией
     */
    public function actionFavorites()
    {
        // Если пользователь не авторизован
        if (Yii::$app->user->isGuest) {
            // Перенаправляем на страницу входа
            return $this->redirect(['site/login']);
        }

        // Создаем запрос для получения избранных книг
        $query = Book::find()
            ->where(['is_hidden' => false])
            ->select(['books.*', 'f.created_at'])
            ->innerJoin('favorites f', 'f.book_id = books.id')
            ->where(['f.user_id' => Yii::$app->user->id])
            ->orderBy(['f.created_at' => SORT_DESC]);

        // Клонируем запрос для подсчета общего количества записей
        $countQuery = clone $query;
        // Создаем объект пагинации
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 12
        ]);

        // Получаем книги с учетом пагинации
        $books = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();

        // Возвращаем представление с данными избранных книг
        return $this->render('favorites', [
            'books' => $books,
            'pagination' => $pages,
        ]);
    }

    /**
     * Страница истории просмотров
     * Отображает список просмотренных книг с пагинацией
     */
    public function actionHistory()
    {
        // Если пользователь не авторизован
        if (Yii::$app->user->isGuest) {
            // Перенаправляем на страницу входа
            return $this->redirect(['site/login']);
        }

        // Создаем запрос для получения истории просмотров
        $query = Book::find()
            ->where(['is_hidden' => false])
            ->select(['books.*', 'vh.viewed_at'])
            ->innerJoin('view_history vh', 'vh.book_id = books.id')
            ->where(['vh.user_id' => Yii::$app->user->id])
            ->orderBy(['vh.viewed_at' => SORT_DESC]);

        // Клонируем запрос для подсчета общего количества записей
        $countQuery = clone $query;
        // Создаем объект пагинации
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 12
        ]);

        // Получаем историю просмотров с учетом пагинации
        $viewHistory = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();

        // Возвращаем представление с данными истории просмотров
        return $this->render('history', [
            'viewHistory' => $viewHistory,
            'pages' => $pages,
        ]);
    }

    /**
     * Очистка истории просмотров пользователя
     */
    public function actionClearHistory()
    {
        // Если пользователь не авторизован
        if (Yii::$app->user->isGuest) {
            // Перенаправляем на страницу входа
            return $this->redirect(['site/login']);
        }

        // Проверяем, что запрос пришел методом POST
        if (Yii::$app->request->isPost) {
            // Удаляем записи из таблицы истории просмотров
            Yii::$app->db->createCommand()
                ->delete('view_history', ['user_id' => Yii::$app->user->id])
                ->execute();

            // Устанавливаем сообщение об успешной очистке истории
            Yii::$app->session->setFlash('success', 'История просмотров очищена');
        }
        
        // Перенаправляем на страницу истории просмотров
        return $this->redirect(['site/history']);
    }

    /**
     * Верификация email пользователя
     */
    public function actionVerify()
    {
        // Если пользователь не авторизован
        if (Yii::$app->user->isGuest) {
            // Перенаправляем на страницу входа
            return $this->redirect(['site/login']);
        }

        // Получаем текущего пользователя
        $user = Yii::$app->user->identity;
        // Если пользователь уже верифицирован
        if ($user->is_verified) {
            // Перенаправляем на главную страницу
            return $this->redirect(['site/index']);
        }

        // Создаем модель формы верификации
        $model = new VerifyForm();
        // Если форма загружена и данные валидны
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // Верифицируем аккаунт пользователя
            $user->verifyAccount();
            // Устанавливаем сообщение об успешной верификации
            Yii::$app->session->setFlash('success', 'Ваш email успешно подтвержден!');
            // Перенаправляем на главную страницу
            return $this->redirect(['site/index']);
        }

        // Возвращаем представление с моделью формы верификации
        return $this->render('verify', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    /**
     * Повторная отправка кода верификации
     */
    public function actionResendCode()
    {
        // Если пользователь не авторизован
        if (Yii::$app->user->isGuest) {
            // Перенаправляем на страницу входа
            return $this->redirect(['site/login']);
        }

        // Получаем текущего пользователя
        $user = Yii::$app->user->identity;
        // Если пользователь уже верифицирован
        if ($user->is_verified) {
            // Перенаправляем на главную страницу
            return $this->redirect(['site/index']);
        }

        // Генерируем новый код верификации
        $user->generateVerificationCode();
        // Отправляем email с кодом верификации
        $user->sendVerificationEmail();
        
        // Устанавливаем сообщение об успешной отправке кода
        Yii::$app->session->setFlash('success', 'Новый код подтверждения отправлен на ваш email.');
        // Перенаправляем на страницу верификации
        return $this->redirect(['site/verify']);
    }

    /**
     * Выход из системы
     */
    public function actionLogout()
    {
        // Выходим из системы
        Yii::$app->user->logout();
        // Перенаправляем на главную страницу
        return $this->goHome();
    }

    /**
     * Форма создания/редактирования книги
     */
    public function actionBookForm($id = null)
    {
        // Если ID книги указан
        if ($id !== null) {
            // Находим книгу по ID
            $model = Book::findOne($id);
            // Если книга не найдена, выбрасываем исключение
            if ($model === null) {
                throw new NotFoundHttpException('Книга не найдена.');
            }
        } else {
            // Иначе создаем новую модель книги
            $model = new Book();
        }

        // Получаем список категорий для выпадающего списка
        $categories = ArrayHelper::map(Category::find()->all(), 'id', 'name');

        // Если форма загружена
        if ($model->load(Yii::$app->request->post())) {
            // Загрузка файлов
            $model->coverFile = UploadedFile::getInstance($model, 'coverFile');
            $model->pdfFile = UploadedFile::getInstance($model, 'pdfFile');

            // Если модель сохранена и файлы загружены
            if ($model->save() && $model->upload()) {
                // Устанавливаем сообщение об успешном сохранении книги
                Yii::$app->session->setFlash('success', 'Книга успешно сохранена.');
                // Перенаправляем в админку
                return $this->redirect(['site/admin']);
            }
        }

        // Возвращаем представление с моделью формы книги
        return $this->render('book-form', [
            'model' => $model,
            'categories' => $categories,
        ]);
    }

    /**
     * Отдача файлов из директории uploads
     * @param string $path путь к файлу
     * @return \yii\web\Response
     * @throws NotFoundHttpException если файл не найден
     */
    public function actionUploads($path)
    {
        // Формируем полный путь к файлу
        $filePath = Yii::getAlias('@webroot/uploads/') . $path;
        // Если файл существует
        if (file_exists($filePath)) {
            // Отправляем файл пользователю
            return Yii::$app->response->sendFile($filePath, basename($filePath), ['inline' => true]);
        }
        // Если файл не найден, выбрасываем исключение
        throw new NotFoundHttpException('Файл не найден.');
    }

    /**
     * Изменение пароля пользователя
     */
    public function actionChangePassword()
    {
        // Устанавливаем формат ответа JSON
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Если пользователь не авторизован
        if (Yii::$app->user->isGuest) {
            // Возвращаем сообщение об ошибке
            return ['success' => false, 'message' => 'Необходима авторизация'];
        }

        // Находим пользователя по ID
        $user = User::findOne(Yii::$app->user->id);
        // Создаем модель формы изменения пароля
        $model = new ChangePasswordForm($user);

        // Если форма загружена
        if ($model->load(Yii::$app->request->post())) {
            // Если пароль успешно изменен
            if ($model->changePassword()) {
                // Выходим из системы
                Yii::$app->user->logout();
                // Возвращаем успешный ответ с сообщением и редиректом
                return [
                    'success' => true,
                    'message' => 'Пароль успешно изменен. Пожалуйста, войдите снова.',
                    'redirect' => \yii\helpers\Url::to(['site/login'])
                ];
            } else {
                // Возвращаем ответ с ошибкой
                return [
                    'success' => false,
                    'message' => 'Ошибка при изменении пароля',
                    'errors' => $model->errors
                ];
            }
        }

        // Возвращаем сообщение об ошибке
        return ['success' => false, 'message' => 'Неверный запрос'];
    }

    /**
     * Обновление контента страницы через AJAX
     * @return array
     */
    public function actionUpdateContent()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Получаем данные из POST запроса
        $identifier = Yii::$app->request->post('identifier');
        $content = Yii::$app->request->post('content');
        $page = Yii::$app->request->post('page', 'site/index'); // Добавляем параметр страницы
        
        if (!$identifier || !$content) {
            return [
                'success' => false,
                'message' => 'Отсутствуют необходимые данные'
            ];
        }

        try {
            // Логируем параметры запроса
            Yii::debug([
                'action' => 'updateContent',
                'page' => $page,
                'identifier' => $identifier,
                'content' => $content
            ]);
            
            // Находим или создаем новую запись в PageContent
            $model = PageContent::findOne([
                'page_url' => $page,
                'block_identifier' => $identifier
            ]);

            if (!$model) {
                $model = new PageContent([
                    'page_url' => $page,
                    'block_identifier' => $identifier
                ]);
            }

            // Обновляем контент
            $model->content = $content;

            if ($model->save()) {
                // Очищаем кэш для этого блока
                $cacheKey = "page_content_{$page}_{$identifier}";
                Yii::$app->cache->delete($cacheKey);
                
                return [
                    'success' => true,
                    'message' => 'Контент успешно обновлен'
                ];
            } else {
                Yii::error('Ошибка сохранения PageContent: ' . print_r($model->errors, true));
                return [
                    'success' => false,
                    'message' => 'Ошибка при сохранении: ' . implode(', ', $model->getErrorSummary(true))
                ];
            }
        } catch (\Exception $e) {
            Yii::error('Исключение при сохранении PageContent: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Произошла ошибка при сохранении'
            ];
        }
    }
}
