<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса Model
use yii\base\Model;

// Класс формы изменения пароля пользователя
class ChangePasswordForm extends Model
{
    // Публичное свойство для текущего пароля
    public $current_password;
    // Публичное свойство для нового пароля
    public $new_password;
    // Публичное свойство для подтверждения нового пароля
    public $new_password_repeat;

    // Приватное свойство для хранения объекта пользователя
    private $_user;

    /**
     * Конструктор класса
     * @param User $user - объект пользователя
     * @param array $config - дополнительные параметры конфигурации
     */
    public function __construct($user, $config = [])
    {
        // Сохраняем объект пользователя
        $this->_user = $user;
        // Вызываем родительский конструктор
        parent::__construct($config);
    }

    /**
     * Определение правил валидации полей формы
     */
    public function rules()
    {
        // Возвращает массив правил валидации
        return [
            // Все поля обязательны для заполнения
            [['current_password', 'new_password', 'new_password_repeat'], 'required'],
            // Проверка текущего пароля через кастомный валидатор
            ['current_password', 'validateCurrentPassword'],
            // Новый пароль должен быть не менее 6 символов
            ['new_password', 'string', 'min' => 6],
            // Подтверждение пароля должно совпадать с новым паролем
            ['new_password_repeat', 'compare', 'compareAttribute' => 'new_password', 'message' => 'Пароли не совпадают.'],
        ];
    }

    /**
     * Определение пользовательских названий атрибутов
     */
    public function attributeLabels()
    {
        // Возвращает массив меток атрибутов
        return [
            'current_password' => 'Текущий пароль', // Метка для текущего пароля
            'new_password' => 'Новый пароль', // Метка для нового пароля
            'new_password_repeat' => 'Подтверждение нового пароля', // Метка для подтверждения пароля
        ];
    }

    /**
     * Метод валидации текущего пароля
     * @param string $attribute - проверяемый атрибут
     * @param array $params - дополнительные параметры
     */
    public function validateCurrentPassword($attribute, $params)
    {
        // Проверяем, нет ли других ошибок валидации
        if (!$this->hasErrors()) {
            // Проверяем правильность текущего пароля
            if (!$this->_user->validatePassword($this->current_password)) {
                // Добавляем ошибку, если пароль неверный
                $this->addError($attribute, 'Неверный текущий пароль.');
            }
        }
    }

    /**
     * Метод смены пароля пользователя
     */
    public function changePassword()
    {
        // Проверяем валидность данных формы
        if ($this->validate()) {
            // Устанавливаем новый пароль пользователю
            $this->_user->setPassword($this->new_password);
            // Сохраняем изменения без валидации
            return $this->_user->save(false);
        }
        return false;
    }
}
