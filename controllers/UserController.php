<?php

// Определяем пространство имен для контроллера
namespace app\controllers;

// Импортируем основной класс Yii
use Yii;
// Импортируем базовый контроллер
use yii\web\Controller;
// Импортируем модель User
use app\models\User;
// Импортируем модель LoginForm для авторизации
use app\models\LoginForm;
// Импортируем модель NewRegisterForm для регистрации
use app\models\NewRegisterForm;

/**
 * Контроллер управления пользователями
 * Обработка профиля, настроек и действий пользователя
 */
class UserController extends Controller
{
    /**
     * Настройка поведения контроллера
     * - Доступ только для авторизованных пользователей
     * - Проверка CSRF для POST запросов
     */
    public function behaviors()
    {
        // Возвращаем массив конфигураций поведения
        return [
            'access' => [
                'class' => AccessControl::class, // Класс контроля доступа
                'rules' => [
                    [
                        'allow' => true, // Разрешить доступ
                        'roles' => ['@'], // Только для авторизованных пользователей
                        'actions' => ['profile', 'settings', 'update-profile', 'change-password', 'upload-avatar', 'register', 'login', 'logout', 'test-db-connection'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class, // Класс фильтрации HTTP методов
                'actions' => [
                    'update-profile' => ['post'],
                    'change-password' => ['post'],
                    'upload-avatar' => ['post'],
                    'register' => ['post'],
                    'login' => ['post'],
                    'logout' => ['post'],
                    'test-db-connection' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Регистрация пользователя
     * Обработка формы регистрации
     */
    public function actionRegister()
    {
        // Создаем модель формы регистрации
        $model = new NewRegisterForm();

        // Если форма загружена и регистрация успешна
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            // Перенаправляем на страницу авторизации
            return $this->redirect(['login']);
        }

        // Возвращаем представление с моделью формы регистрации
        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Авторизация пользователя
     * Обработка формы авторизации
     */
    public function actionLogin()
    {
        // Создаем модель формы авторизации
        $model = new LoginForm();

        // Если форма загружена и авторизация успешна
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // Возвращаем пользователя на предыдущую страницу
            return $this->goBack();
        }

        // Очищаем поле пароля
        $model->password = '';

        // Возвращаем представление с моделью формы авторизации
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Выход из системы
     * Удаление авторизации пользователя
     */
    public function actionLogout()
    {
        // Выходим из системы
        Yii::$app->user->logout();
        // Перенаправляем на главную страницу
        return $this->goHome();
    }

    /**
     * Тестирование подключения к базе данных
     * Создание тестового пользователя
     */
    public function actionTestDbConnection()
    {
        // Создаем нового пользователя
        $user = new User();
        $user->username = 'testuser';
        $user->email = 'testuser@example.com';
        // Генерируем хэш пароля
        $user->password = Yii::$app->security->generatePasswordHash('password');

        // Сохраняем пользователя и проверяем успешность
        if ($user->save()) {
            return 'User saved successfully';
        } else {
            return 'Failed to save user: ' . json_encode($user->errors);
        }
    }
}
