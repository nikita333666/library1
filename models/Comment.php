<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса ActiveRecord
use yii\db\ActiveRecord;

/**
 * Класс модели комментария к книге
 * Описание свойств модели:
 * @property int $id - Уникальный идентификатор комментария
 * @property int $user_id - ID пользователя, оставившего комментарий
 * @property int $book_id - ID книги, к которой оставлен комментарий
 * @property string $text - Текст комментария
 * @property string $created_at - Дата и время создания комментария
 */
class Comment extends ActiveRecord
{
    /**
     * Определение имени таблицы в базе данных
     */
    public static function tableName()
    {
        // Возвращает имя таблицы comments
        return 'comments';
    }

    /**
     * Определение правил валидации полей
     */
    public function rules()
    {
        // Возвращает массив правил валидации
        return [
            // Обязательные поля для заполнения
            [['user_id', 'book_id', 'text'], 'required'],
            // Поля user_id и book_id должны быть целыми числами
            [['user_id', 'book_id'], 'integer'],
            // Поле text должно быть строкой
            [['text'], 'string'],
            // Поле created_at может принимать любые безопасные значения
            [['created_at'], 'safe']
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
}
