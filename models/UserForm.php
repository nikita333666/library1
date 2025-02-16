<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса Model
use yii\base\Model;

/**
 * Класс модели формы редактирования профиля пользователя
 * Обрабатывает создание и редактирование пользователей
 */
class UserForm extends Model
{
    // Имя пользователя
    public $username;
    // Email пользователя
    public $email;
    // Новый пароль
    public $new_password;
    // Подтверждение нового пароля
    public $new_password_repeat;
    // Роль пользователя
    public $is_admin;

    // Приватное свойство для хранения модели пользователя
    private $_user;

    /**
     * Конструктор формы
     * @param User|null $user - Модель пользователя (null для нового пользователя)
     * @param array $config - Дополнительные параметры конфигурации
     */
    public function __construct($user = null, $config = [])
    {
        // Если передана модель пользователя
        if ($user !== null) {
            // Сохраняем модель пользователя
            $this->_user = $user;
            // Заполняем поля формы данными пользователя
            $this->username = $user->username;
            $this->email = $user->email;
            $this->is_admin = $user->is_admin;
        } else {
            // Для нового пользователя устанавливаем роль по умолчанию
            $this->is_admin = User::ROLE_USER;
        }
        // Вызов родительского конструктора
        parent::__construct($config);
    }

    /**
     * Определение правил валидации полей
     */
    public function rules()
    {
        // Возвращает массив правил валидации
        return [
            // Обязательные поля для заполнения
            [['username', 'email'], 'required'],
            // Ограничение длины строковых полей
            [['username', 'email'], 'string', 'max' => 255],
            // Проверка корректности email
            [['email'], 'email'],
            // Проверка уникальности имени пользователя
            [['username'], 'unique', 'targetClass' => User::class, 'filter' => function ($query) {
                // Исключаем текущего пользователя при проверке
                if ($this->_user !== null) {
                    $query->andWhere(['not', ['id' => $this->_user->id]]);
                }
            }],
            // Проверка уникальности email
            [['email'], 'unique', 'targetClass' => User::class, 'filter' => function ($query) {
                // Исключаем текущего пользователя при проверке
                if ($this->_user !== null) {
                    $query->andWhere(['not', ['id' => $this->_user->id]]);
                }
            }],
            // Минимальная длина пароля
            [['new_password'], 'string', 'min' => 6],
            // Проверка совпадения паролей
            [['new_password_repeat'], 'compare', 'compareAttribute' => 'new_password'],
            // Проверка допустимых значений роли
            [['is_admin'], 'in', 'range' => [User::ROLE_USER, User::ROLE_ADMIN]],
            // Пароль обязателен только для новых пользователей
            [['new_password', 'new_password_repeat'], 'required', 'when' => function($model) {
                return $model->_user === null;
            }],
        ];
    }

    /**
     * Определение пользовательских названий атрибутов
     */
    public function attributeLabels()
    {
        // Возвращает массив меток атрибутов
        return [
            'username' => 'Имя пользователя', // Метка для имени пользователя
            'email' => 'Email', // Метка для email
            'new_password' => 'Пароль', // Метка для пароля
            'new_password_repeat' => 'Повторите пароль', // Метка для подтверждения пароля
            'is_admin' => 'Роль', // Метка для роли
        ];
    }

    /**
     * Метод получения списка доступных ролей
     * @return array - массив доступных ролей
     */
    public function getRolesList()
    {
        // Владелец может назначать только пользователей и администраторов
        return [
            User::ROLE_USER => 'Пользователь', // Роль обычного пользователя
            User::ROLE_ADMIN => 'Администратор', // Роль администратора
        ];
    }

    /**
     * Метод сохранения данных формы в модель пользователя
     * @return bool - результат сохранения
     */
    public function save()
    {
        // Проверка валидности данных формы
        if (!$this->validate()) {
            return false;
        }

        // Если это новый пользователь
        if ($this->_user === null) {
            // Создаем новую модель пользователя
            $this->_user = new User();
            // Устанавливаем хеш пароля
            $this->_user->password_hash = Yii::$app->security->generatePasswordHash($this->new_password);
        } elseif (!empty($this->new_password)) {
            // Если указан новый пароль, обновляем его хеш
            $this->_user->password_hash = Yii::$app->security->generatePasswordHash($this->new_password);
        }

        // Обновляем основные данные пользователя
        $this->_user->username = $this->username;
        $this->_user->email = $this->email;

        // Обработка роли пользователя
        if (Yii::$app->user->identity->isOwner()) {
            // Для владельца проверяем допустимость выбранной роли
            $this->_user->is_admin = in_array($this->is_admin, [User::ROLE_USER, User::ROLE_ADMIN]) 
                ? $this->is_admin 
                : User::ROLE_USER;
        } elseif ($this->_user->isNewRecord) {
            // Для новых пользователей устанавливаем роль пользователя
            $this->_user->is_admin = User::ROLE_USER;
        }

        // Сохраняем модель пользователя
        return $this->_user->save();
    }

    /**
     * Метод получения модели пользователя
     * @return User|null - объект пользователя или null
     */
    public function getUser()
    {
        // Возвращаем связанную модель пользователя
        return $this->_user;
    }
}
