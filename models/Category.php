<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт базового класса ActiveRecord для работы с БД
use yii\db\ActiveRecord;
// Импорт вспомогательного класса для работы с массивами
use yii\helpers\ArrayHelper;

/**
 * Класс модели категории книг
 * Описание свойств модели:
 * @property int $id - Уникальный идентификатор категории
 * @property string $name - Название категории
 */
class Category extends ActiveRecord
{
    /**
     * Определение имени таблицы в базе данных
     */
    public static function tableName()
    {
        // Возвращает имя таблицы categories
        return 'categories';
    }

    /**
     * Определение правил валидации полей
     */
    public function rules()
    {
        // Возвращает массив правил валидации
        return [
            // Поле name обязательно для заполнения
            [['name'], 'required'],
            // Поле name должно быть строкой не более 255 символов
            [['name'], 'string', 'max' => 255],
            // Поле name должно быть уникальным
            [['name'], 'unique'],
        ];
    }

    /**
     * Определение пользовательских названий атрибутов
     */
    public function attributeLabels()
    {
        // Возвращает массив меток атрибутов
        return [
            'id' => 'ID', // Идентификатор категории
            'name' => 'Название категории', // Название категории
        ];
    }

    /**
     * Определение связи с моделью Book
     */
    public function getBooks()
    {
        // Возвращает связь один-ко-многим с моделью Book
        return $this->hasMany(Book::class, ['category_id' => 'id']);
    }

    /**
     * Получение количества книг в текущей категории
     */
    public function getBooksCount()
    {
        // Возвращает количество книг в категории через связь
        return $this->getBooks()->count();
    }

    /**
     * Получение списка всех категорий для выпадающего списка
     */
    public static function getDropdownList()
    {
        // Формирует массив вида id => name для всех категорий
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }

    /**
     * Получение списка популярных категорий
     */
    public static function getPopular($limit = 5)
    {
        // Возвращает категории, отсортированные по количеству книг
        return static::find()
            // Выбираем все поля категорий и считаем количество книг
            ->select(['categories.*', 'COUNT(books.id) as book_count'])
            // Присоединяем таблицу книг
            ->leftJoin('books', 'books.category_id = categories.id')
            // Группируем по ID категории
            ->groupBy('categories.id')
            // Сортируем по количеству книг по убыванию
            ->orderBy(['book_count' => SORT_DESC])
            // Ограничиваем количество результатов
            ->limit($limit)
            // Получаем результат
            ->all();
    }
}
