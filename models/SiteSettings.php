<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса Model
use yii\base\Model;

/**
 * Класс модели настроек сайта
 * Управляет основными настройками сайта
 */
class SiteSettings extends Model
{
    // Заголовок сайта
    public $title;
    // Ключевые слова сайта
    public $keywords;
    // Описание сайта
    public $description;

    /**
     * Определение правил валидации полей
     */
    public function rules()
    {
        // Возвращает массив правил валидации
        return [
            // Все поля должны быть строками
            [['title', 'keywords', 'description'], 'string'],
            // Заголовок обязателен для заполнения
            [['title'], 'required'],
        ];
    }

    /**
     * Определение пользовательских названий атрибутов
     */
    public function attributeLabels()
    {
        // Возвращает массив меток атрибутов
        return [
            'title' => 'Заголовок сайта', // Метка для заголовка
            'keywords' => 'Ключевые слова', // Метка для ключевых слов
            'description' => 'Описание', // Метка для описания
        ];
    }

    /**
     * Метод загрузки настроек из хранилища
     * @return bool - результат загрузки
     */
    public function loadSettings()
    {
        // TODO: Реализовать загрузку из хранилища
        return true;
    }

    /**
     * Метод сохранения настроек в хранилище
     * @return bool - результат сохранения
     */
    public function saveSettings()
    {
        // Проверка валидности данных
        if ($this->validate()) {
            // TODO: Реализовать сохранение в хранилище
            return true;
        }
        // Возврат false при неуспешной валидации
        return false;
    }
}
