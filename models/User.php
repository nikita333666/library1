<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса ActiveRecord
use yii\db\ActiveRecord;
// Импорт интерфейса для аутентификации пользователей
use yii\web\IdentityInterface;

/**
 * Класс модели пользователя
 * Реализует функционал работы с пользователями системы
 *
 * @property int $id - Уникальный идентификатор пользователя
 * @property string $username - Имя пользователя
 * @property string $email - Email пользователя
 * @property string $password_hash - Хеш пароля
 * @property string $auth_key - Ключ аутентификации
 * @property int $is_admin - Флаг роли пользователя
 */
class User extends ActiveRecord implements IdentityInterface
{
    // Константа для роли обычного пользователя
    const ROLE_USER = 0;
    // Константа для роли администратора
    const ROLE_ADMIN = 1;
    // Константа для роли владельца
    const ROLE_OWNER = 2;

    /**
     * Определение имени таблицы в базе данных
     */
    public static function tableName()
    {
        // Возвращает имя таблицы users
        return 'users';
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
            // Поле is_admin должно быть целым числом
            [['is_admin'], 'integer'],
            // Ограничение длины строковых полей
            [['username', 'email', 'password_hash', 'auth_key'], 'string', 'max' => 255],
            // Проверка уникальности имени пользователя
            [['username'], 'unique'],
            // Проверка уникальности email
            [['email'], 'unique'],
        ];
    }

    /**
     * Определение пользовательских названий атрибутов
     */
    public function attributeLabels()
    {
        // Возвращает массив меток атрибутов
        return [
            'id' => 'ID', // Идентификатор пользователя
            'username' => 'Имя пользователя', // Имя пользователя
            'email' => 'Email', // Email пользователя
            'password_hash' => 'Пароль', // Хеш пароля
            'auth_key' => 'Ключ аутентификации', // Ключ аутентификации
            'is_admin' => 'Администратор', // Флаг администратора
        ];
    }

    /**
     * Метод установки пароля пользователя
     * @param string $password - пароль в открытом виде
     */
    public function setPassword($password)
    {
        // Генерация хеша пароля и сохранение его в модели
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        // Генерируем auth_key при установке пароля
        $this->generateAuthKey();
    }

    /**
     * Генерирует случайный ключ аутентификации
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Метод проверки правильности пароля
     * @param string $password - проверяемый пароль
     * @return bool - результат проверки
     */
    public function validatePassword($password)
    {
        // Проверка соответствия пароля сохраненному хешу
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Метод получения ключа аутентификации
     * Реализация метода интерфейса IdentityInterface
     * @return string ключ аутентификации
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Метод проверки ключа аутентификации
     * Реализация метода интерфейса IdentityInterface
     * @param string $authKey
     * @return bool результат проверки
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Метод проверки является ли пользователь администратором
     * @return bool - результат проверки
     */
    public function isAdmin()
    {
        // Проверка имеет ли пользователь роль администратора или владельца
        return $this->is_admin == self::ROLE_ADMIN || $this->is_admin == self::ROLE_OWNER;
    }

    /**
     * Метод проверки является ли пользователь владельцем
     * @return bool - результат проверки
     */
    public function isOwner()
    {
        // Проверка имеет ли пользователь роль владельца
        return $this->is_admin == self::ROLE_OWNER;
    }

    /**
     * Метод получения названия роли пользователя
     * @return string - название роли
     */
    public function getRoleName()
    {
        // Определение названия роли на основе значения is_admin
        switch ($this->is_admin) {
            case self::ROLE_OWNER:
                return 'Владелец';
            case self::ROLE_ADMIN:
                return 'Администратор';
            default:
                return 'Пользователь';
        }
    }

    /**
     * Метод проверки может ли пользователь управлять администраторами
     * @return bool - результат проверки
     */
    public function canManageAdmins()
    {
        // Только владелец может управлять администраторами
        return $this->isOwner();
    }

    /**
     * Метод поиска пользователя по ID
     * Реализация метода интерфейса IdentityInterface
     * @param int $id - ID пользователя
     * @return User|null - найденный пользователь или null
     */
    public static function findIdentity($id)
    {
        // Поиск пользователя по ID
        return static::findOne(['id' => $id]);
    }

    /**
     * Метод поиска пользователя по токену доступа
     * Реализация метода интерфейса IdentityInterface
     * В данной реализации не используется
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // Токены доступа не используются
        return null;
    }

    /**
     * Метод получения ID пользователя
     * Реализация метода интерфейса IdentityInterface
     * @return int - ID пользователя
     */
    public function getId()
    {
        // Возвращает ID пользователя
        return $this->id;
    }

    /**
     * Метод поиска пользователя по имени пользователя
     * @param string $username - имя пользователя
     * @return User|null - найденный пользователь или null
     */
    public static function findByUsername($username)
    {
        // Поиск пользователя по имени пользователя
        return static::findOne(['username' => $username]);
    }
}