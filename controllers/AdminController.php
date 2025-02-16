<?php
// Объявление PHP кода

// Определение пространства имен для контроллера
namespace app\controllers;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса Controller
use yii\web\Controller;
// Импорт класса AccessControl для управления доступом
use yii\filters\AccessControl;
// Импорт класса ActiveDataProvider для работы с данными
use yii\data\ActiveDataProvider;
// Импорт класса UploadedFile для работы с загруженными файлами
use yii\web\UploadedFile;
// Импорт исключения NotFoundHttpException для обработки ошибок
use yii\web\NotFoundHttpException;
// Импорт исключения ForbiddenHttpException для обработки ошибок доступа
use yii\web\ForbiddenHttpException;
// Импорт модели User
use app\models\User;
// Импорт модели Book
use app\models\Book;
// Импорт модели Category
use app\models\Category;
// Импорт модели UserForm
use app\models\UserForm;
// Импорт модели BookSearch
use app\models\BookSearch;

/**
 * Класс контроллера административной панели
 * Управляет книгами, категориями и пользователями
 */
class AdminController extends Controller
{
    /**
     * Настройка поведения контроллера
     * - Доступ только для администраторов
     * - Проверка прав доступа
     */
    public function behaviors()
    {
        // Возвращает массив конфигураций поведения
        return [
            'access' => [
                'class' => AccessControl::className(), // Класс контроля доступа
                'rules' => [
                    [
                        'allow' => true, // Разрешить доступ
                        'roles' => ['@'], // Только для авторизованных пользователей
                        'matchCallback' => function ($rule, $action) {
                            // Проверка, что пользователь не гость и является администратором
                            return !Yii::$app->user->isGuest && (Yii::$app->user->identity->is_admin == 1 || Yii::$app->user->identity->is_admin == 2);
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Проверка доступа перед выполнением действия
     * - Проверка прав доступа
     * - Перенаправление на страницу входа, если пользователь не авторизован
     */
    public function beforeAction($action)
    {
        // Проверка базовых условий перед выполнением действия
        if (!parent::beforeAction($action)) {
            return false;
        }

        // Проверяем, имеет ли пользователь доступ к управлению администраторами
        if (in_array($action->id, ['delete-user', 'update-user']) && !Yii::$app->user->identity->canManageAdmins()) {
            $targetUser = User::findOne(Yii::$app->request->get('id'));
            if ($targetUser && $targetUser->isAdmin()) {
                Yii::$app->session->setFlash('error', 'У вас нет прав для управления администраторами');
                return $this->redirect(['users']);
            }
        }

        return true;
    }

    /**
     * Управление пользователями
     * Список всех пользователей с возможностью управления правами
     */
    public function actionUsers()
    {
        // Создание провайдера данных для пользователей
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(), // Запрос для получения пользователей
            'pagination' => [
                'pageSize' => 10, // Количество пользователей на странице
            ],
        ]);

        // Отображение страницы с пользователями
        return $this->render('users', [
            'dataProvider' => $dataProvider,
            'searchModel' => null, // Если у вас есть модель поиска, замените null на неё
        ]);
    }

    /**
     * Управление книгами
     * Список всех книг с возможностью редактирования
     */
    public function actionBooks()
    {
        // Создаем экземпляр модели поиска
        $searchModel = new BookSearch();
        // Получаем провайдер данных с применением фильтров
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Получаем все категории для фильтра
        $categories = Category::find()->all();

        return $this->render('books', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'categories' => $categories,
        ]);
    }

    /**
     * Управление категориями
     * CRUD операции для категорий
     */
    public function actionCategories()
    {
        // Создание провайдера данных для категорий
        $dataProvider = new ActiveDataProvider([
            'query' => Category::find(), // Запрос для получения категорий
            'pagination' => [
                'pageSize' => 10, // Количество категорий на странице
            ],
        ]);

        // Отображение страницы с категориями
        return $this->render('categories', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Создание нового пользователя
     */
    public function actionCreateUser()
    {
        // Создание новой формы пользователя
        $model = new UserForm();

        // Загрузка данных формы и сохранение пользователя
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Пользователь успешно создан');
            return $this->redirect(['users']);
        }

        // Отображение страницы создания пользователя
        return $this->render('create-user', [
            'model' => $model,
        ]);
    }

    /**
     * Редактирование пользователя
     * @param int $id ID пользователя
     */
    public function actionUpdateUser($id)
    {
        // Поиск пользователя по ID
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден.');
        }

        // Запрещаем редактирование собственного профиля в админ панели
        if (Yii::$app->user->id == $user->id) {
            Yii::$app->session->setFlash('error', 'Редактирование собственного профиля в админ панели запрещено. Пожалуйста, используйте страницу профиля.');
            return $this->redirect(['users']);
        }

        // Проверяем права доступа
        if (Yii::$app->user->identity->is_admin == 1) {
            // Обычный админ не может редактировать других админов
            if ($user->is_admin > 0) {
                throw new ForbiddenHttpException('У вас нет прав для редактирования администраторов.');
            }
        } elseif (Yii::$app->user->identity->is_admin == 2) {
            // Владелец не может редактировать других владельцев
            if ($user->is_admin == 2) {
                throw new ForbiddenHttpException('Вы не можете редактировать других владельцев.');
            }
        }

        // Загрузка данных формы и сохранение изменений
        if ($user->load(Yii::$app->request->post())) {
            // Только владелец может менять роли
            if (Yii::$app->user->identity->is_admin != 2) {
                // Если не владелец, то сбрасываем значение is_admin обратно
                $user->is_admin = $user->getOldAttribute('is_admin');
            } else {
                // Владелец может назначать только роли пользователя (0) или админа (1)
                $user->is_admin = min((int)$user->is_admin, 1);
            }

            // Сохранение изменений
            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'Пользователь успешно обновлен.');
                return $this->redirect(['users']);
            }
        }

        // Отображение страницы редактирования пользователя
        return $this->render('update-user', [
            'model' => $user,
        ]);
    }

    /**
     * Удаление пользователя
     * @param int $id ID пользователя
     */
    public function actionDeleteUser($id)
    {
        // Поиск пользователя по ID
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден.');
        }

        // Проверяем права доступа
        if (Yii::$app->user->identity->is_admin == 1) {
            // Обычный админ не может удалять админов
            if ($user->is_admin > 0) {
                throw new ForbiddenHttpException('У вас нет прав для удаления администраторов.');
            }
        } elseif (Yii::$app->user->identity->is_admin == 2) {
            // Владелец не может удалять других владельцев
            if ($user->is_admin == 2) {
                throw new ForbiddenHttpException('Вы не можете удалять других владельцев.');
            }
        }

        // Удаление пользователя
        $user->delete();
        Yii::$app->session->setFlash('success', 'Пользователь успешно удален.');
        return $this->redirect(['users']);
    }

    /**
     * Создание новой категории
     * CRUD операции для категорий
     */
    public function actionCreateCategory()
    {
        // Создание новой модели категории
        $model = new Category();

        // Загрузка данных формы и сохранение категории
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Категория успешно создана.');
            return $this->redirect(['categories']);
        }

        // Отображение страницы создания категории
        return $this->render('create-category', [
            'model' => $model,
        ]);
    }

    /**
     * Редактирование категории
     * @param int $id ID категории
     */
    public function actionUpdateCategory($id)
    {
        // Поиск категории по ID
        $model = Category::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Категория не найдена.');
        }

        // Загрузка данных формы и сохранение изменений
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Категория успешно обновлена.');
            return $this->redirect(['categories']);
        }

        // Отображение страницы редактирования категории
        return $this->render('update-category', [
            'model' => $model,
        ]);
    }

    /**
     * Удаление категории
     * @param int $id ID категории
     * @return \yii\web\Response
     */
    public function actionDeleteCategory($id)
    {
        // Поиск категории по ID
        $category = Category::findOne($id);
        if ($category === null) {
            throw new NotFoundHttpException('Категория не найдена.');
        }

        // Проверяем, есть ли книги в этой категории
        $booksCount = Book::find()->where(['category_id' => $id])->count();
        if ($booksCount > 0) {
            Yii::$app->session->setFlash('error', 'Категорию нельзя удалить, так как она связана с книгами (' . $booksCount . ' шт.). Пожалуйста, сначала удалите или переместите все книги из этой категории.');
            return $this->redirect(['categories']);
        }

        // Удаление категории
        if ($category->delete()) {
            Yii::$app->session->setFlash('success', 'Категория успешно удалена.');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении категории.');
        }

        return $this->redirect(['categories']);
    }

    /**
     * Создание новой книги
     */
    public function actionCreateBook()
    {
        // Создание новой модели книги
        $model = new Book();

        // Загрузка данных формы
        if ($model->load(Yii::$app->request->post())) {
            // Получаем загруженные файлы
            $model->coverFile = UploadedFile::getInstance($model, 'coverFile');
            $model->pdfFile = UploadedFile::getInstance($model, 'pdfFile');

            // Сначала валидируем модель
            if ($model->validate()) {
                // Затем загружаем файлы
                if ($model->upload()) {
                    // И только потом сохраняем модель
                    if ($model->save(false)) {
                        Yii::$app->session->setFlash('success', 'Книга успешно создана.');
                        return $this->redirect(['books']);
                    }
                }
            }
            
            // Если что-то пошло не так, показываем ошибки
            Yii::$app->session->setFlash('error', 'Ошибка при создании книги: ' . implode(', ', $model->getErrorSummary(true)));
        }

        // Отображение страницы создания книги
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Обновление существующей книги
     */
    public function actionUpdateBook($id)
    {
        // Поиск книги по ID
        $model = Book::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Книга не найдена.');
        }

        // Сохранение старых значений файлов
        $oldCoverImage = $model->cover_image;
        $oldPdfFile = $model->pdf_file;

        // Загрузка данных формы
        if ($model->load(Yii::$app->request->post())) {
            // Получаем загруженные файлы
            $model->coverFile = UploadedFile::getInstance($model, 'coverFile');
            $model->pdfFile = UploadedFile::getInstance($model, 'pdfFile');

            // Если новые файлы не были загружены, сохраняем старые
            if (!$model->coverFile) {
                $model->cover_image = $oldCoverImage;
            }
            if (!$model->pdfFile) {
                $model->pdf_file = $oldPdfFile;
            }

            // Сначала валидируем модель
            if ($model->validate()) {
                // Затем загружаем файлы
                if ($model->upload()) {
                    // И только потом сохраняем модель
                    if ($model->save(false)) {
                        Yii::$app->session->setFlash('success', 'Книга успешно обновлена.');
                        return $this->redirect(['/book/view', 'id' => $model->seo_url ?: $model->id]);
                    }
                }
            }
            
            // Если что-то пошло не так, показываем ошибки
            Yii::$app->session->setFlash('error', 'Ошибка при обновлении книги.');
        }

        // Отображение страницы обновления книги
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Удаление книги
     * @param int $id ID книги
     */
    public function actionDeleteBook($id)
    {
        // Поиск книги по ID
        $model = Book::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Книга не найдена.');
        }

        // Удаление записи из базы данных
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Книга успешно удалена.');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении книги.');
        }

        return $this->redirect(['books']);
    }

    /**
     * Переключение видимости книги
     */
    public function actionToggleBookVisibility($id)
    {
        // Поиск книги по ID
        $book = Book::findOne($id);
        if ($book) {
            // Изменение видимости книги
            $book->is_hidden = !$book->is_hidden;
            if ($book->save()) {
                Yii::$app->session->setFlash('success', 
                    $book->is_hidden ? 'Книга скрыта' : 'Книга восстановлена'
                );
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при изменении видимости книги');
            }
        }
        return $this->redirect(['admin/books']);
    }


}
