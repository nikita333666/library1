<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса ActiveRecord для работы с базой данных
use yii\db\ActiveRecord;

// Класс AboutContent для работы с контентом страницы "О нас", наследуется от ActiveRecord
class AboutContent extends ActiveRecord
{
    // Метод определяет имя таблицы в базе данных
    public static function tableName()
    {
        // Возвращает имя таблицы about_content
        return 'about_content';
    }

    // Метод определяет правила валидации для полей модели
    public function rules()
    {
        // Возвращает массив правил валидации
        return [
            // Поля section и identifier обязательны для заполнения
            [['section', 'identifier'], 'required'],
            // Поле content должно быть строкового типа
            [['content'], 'string'],
            // Поля created_at и updated_at могут принимать любые безопасные значения
            [['created_at', 'updated_at'], 'safe'],
            // Поля section и identifier должны быть строками не более 255 символов
            [['section', 'identifier'], 'string', 'max' => 255],
            // Комбинация полей section и identifier должна быть уникальной
            [['section', 'identifier'], 'unique', 'targetAttribute' => ['section', 'identifier']],
        ];
    }

    // Метод определяет отображаемые названия полей
    public function attributeLabels()
    {
        // Возвращает массив меток атрибутов
        return [
            'id' => 'ID', // Идентификатор записи
            'section' => 'Секция', // Название секции
            'identifier' => 'Идентификатор', // Идентификатор контента
            'content' => 'Содержимое', // Содержимое секции
            'created_at' => 'Создано', // Дата создания
            'updated_at' => 'Обновлено', // Дата обновления
        ];
    }

    // Статический метод для получения контента по секции и идентификатору
    public static function getContent($section, $identifier)
    {
        // Возвращает одну запись, соответствующую указанной секции и идентификатору
        return static::findOne(['section' => $section, 'identifier' => $identifier]);
    }
}
