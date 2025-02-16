<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса ActiveRecord
use yii\db\ActiveRecord;

/**
 * Класс модели истории просмотров книг
 * Отслеживает просмотры книг пользователями
 * 
 * @property int $id - Уникальный идентификатор записи
 * @property int $user_id - ID пользователя, просмотревшего книгу
 * @property int $book_id - ID просмотренной книги
 * @property string $viewed_at - Дата и время просмотра
 */
class ViewHistory extends ActiveRecord
{
    /**
     * Определение имени таблицы в базе данных
     */
    public static function tableName()
    {
        // Возвращает имя таблицы view_history
        return 'view_history';
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
            // Поле viewed_at может принимать любые безопасные значения
            [['viewed_at'], 'safe'],
            // Проверка существования пользователя
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            // Проверка существования книги
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['book_id' => 'id']],
        ];
    }

    /**
     * Определение связи с моделью User
     * @return \yii\db\ActiveQuery - объект запроса для связи
     */
    public function getUser()
    {
        // Возвращает связь один-к-одному с моделью User
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Определение связи с моделью Book
     * @return \yii\db\ActiveQuery - объект запроса для связи
     */
    public function getBook()
    {
        // Возвращает связь один-к-одному с моделью Book
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    /**
     * Метод добавления записи в историю просмотров
     * @param int $user_id - ID пользователя
     * @param int $book_id - ID книги
     * @return bool - результат сохранения
     */
    public static function addToHistory($user_id, $book_id)
    {
        // Создание новой записи истории просмотра
        $history = new self([
            'user_id' => $user_id, // ID пользователя
            'book_id' => $book_id, // ID книги
            'viewed_at' => date('Y-m-d H:i:s') // Текущая дата и время
        ]);
        // Сохранение записи в базе данных
        return $history->save();
    }

    /**
     * Метод получения истории просмотров пользователя
     * @param int $user_id - ID пользователя
     * @param int $limit - максимальное количество записей
     * @return array - массив записей истории
     */
    public static function getUserHistory($user_id, $limit = 10)
    {
        // Возвращает последние просмотры пользователя
        return self::find()
            // Фильтрация по ID пользователя
            ->where(['user_id' => $user_id])
            // Сортировка по дате просмотра (сначала новые)
            ->orderBy(['viewed_at' => SORT_DESC])
            // Ограничение количества записей
            ->limit($limit)
            // Получение результатов
            ->all();
    }
}
