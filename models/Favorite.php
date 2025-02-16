<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса ActiveRecord
use yii\db\ActiveRecord;

/**
 * Класс модели избранных книг пользователя
 * Описание свойств модели:
 * @property int $id - Уникальный идентификатор записи
 * @property int $user_id - ID пользователя, добавившего книгу
 * @property int $book_id - ID добавленной книги
 * @property string $created_at - Дата и время добавления в избранное
 */
class Favorite extends ActiveRecord
{
    /**
     * Определение имени таблицы в базе данных
     */
    public static function tableName()
    {
        // Возвращает имя таблицы favorites
        return 'favorites';
    }

    /**
     * Определение правил валидации полей
     */
    public function rules()
    {
        // Возвращает массив правил валидации
        return [
            // Обязательные поля для заполнения
            [['user_id', 'book_id'], 'required'],
            // Поля user_id и book_id должны быть целыми числами
            [['user_id', 'book_id'], 'integer'],
            // Поле created_at может принимать любые безопасные значения
            [['created_at'], 'safe'],
            // Проверка уникальности комбинации пользователь-книга
            [['user_id', 'book_id'], 'unique', 'targetAttribute' => ['user_id', 'book_id']],
            // Проверка существования пользователя
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            // Проверка существования книги
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['book_id' => 'id']],
        ];
    }

    /**
     * Определение связи с моделью User
     */
    public function getUser()
    {
        // Возвращает связь один-к-одному с моделью User
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Определение связи с моделью Book
     */
    public function getBook()
    {
        // Возвращает связь один-к-одному с моделью Book
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    /**
     * Получение списка избранных книг пользователя
     * @param int $userId - ID пользователя
     * @return array - массив избранных книг
     */
    public static function getUserFavorites($userId)
    {
        // Возвращает все избранные книги пользователя, отсортированные по дате добавления
        return static::find()
            // Фильтрация по ID пользователя
            ->where(['user_id' => $userId])
            // Сортировка по дате добавления (сначала новые)
            ->orderBy(['created_at' => SORT_DESC])
            // Получение результатов
            ->all();
    }
}
