<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса Model
use yii\base\Model;

/**
 * Класс модели формы авторизации пользователя
 * Обрабатывает вход пользователя в систему
 */
class LoginForm extends Model
{
    // Имя пользователя для входа
    public $username;
    // Пароль пользователя
    public $password;
    // Флаг "запомнить меня", по умолчанию включен
    public $rememberMe = true;

    // Приватное свойство для кэширования объекта пользователя
    private $_user = false;

    /**
     * Определение правил валидации полей формы
     */
    public function rules()
    {
        // Возвращает массив правил валидации
        return [
            // Обязательные поля для заполнения
            [['username', 'password'], 'required'],
            // Поле rememberMe должно быть булевым
            ['rememberMe', 'boolean'],
            // Проверка пароля через кастомный валидатор
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Метод валидации пароля
     * Проверяет корректность введенного пароля
     * @param string $attribute - проверяемый атрибут
     * @param array $params - дополнительные параметры
     */
    public function validatePassword($attribute, $params)
    {
        // Проверяем, нет ли других ошибок валидации
        if (!$this->hasErrors()) {
            // Получаем объект пользователя
            $user = $this->getUser();

            // Проверяем существование пользователя и правильность пароля
            if (!$user || !$user->validatePassword($this->password)) {
                // Добавляем ошибку, если данные неверны
                $this->addError($attribute, 'Неверное имя пользователя или пароль.');
            }
        }
    }

    /**
     * Метод выполнения входа пользователя в систему
     * @return bool - результат входа
     */
    public function login()
    {
        // Проверяем валидность данных формы
        if ($this->validate()) {
            // Выполняем вход пользователя
            // Если установлен флаг "запомнить меня", то сессия будет жить 30 дней
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        // Возвращаем false при неуспешной валидации
        return false;
    }

    /**
     * Метод получения объекта пользователя
     * @return User|null - объект пользователя или null
     */
    private function getUser()
    {
        // Проверяем, не был ли пользователь уже найден
        if ($this->_user === false) {
            // Ищем пользователя по имени пользователя
            $this->_user = User::findByUsername($this->username);
        }

        // Возвращаем объект пользователя
        return $this->_user;
    }
}
