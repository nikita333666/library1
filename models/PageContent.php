<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса ActiveRecord
use yii\db\ActiveRecord;
// Импорт поведения для автоматического обновления временных меток
use yii\behaviors\TimestampBehavior;
// Импорт класса для работы с SQL выражениями
use yii\db\Expression;

/**
 * Класс модели содержимого страниц
 * Используется для хранения и управления контентом на страницах сайта
 */
class PageContent extends ActiveRecord
{
    /**
     * Определение имени таблицы в базе данных
     */
    public static function tableName()
    {
        // Возвращает имя таблицы page_content
        return 'page_content';
    }

    /**
     * Определение поведений модели
     */
    public function behaviors()
    {
        // Возвращает массив поведений
        return [
            [
                // Использование поведения TimestampBehavior
                'class' => TimestampBehavior::class,
                // Указание атрибута для даты создания
                'createdAtAttribute' => 'created_at',
                // Указание атрибута для даты обновления
                'updatedAtAttribute' => 'updated_at',
                // Установка текущего времени через SQL функцию NOW()
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Определение правил валидации полей
     */
    public function rules()
    {
        // Возвращает массив правил валидации
        return [
            // Обязательные поля для заполнения
            [['page_url', 'block_identifier'], 'required'],
            // Поле content должно быть строкой
            [['content'], 'string'],
            // Поля дат могут принимать любые безопасные значения
            [['created_at', 'updated_at'], 'safe'],
            // Ограничение длины строковых полей
            [['page_url', 'block_identifier'], 'string', 'max' => 255],
        ];
    }

    /**
     * Определение пользовательских названий атрибутов
     */
    public function attributeLabels()
    {
        // Возвращает массив меток атрибутов
        return [
            'id' => 'ID', // Идентификатор записи
            'page_url' => 'Страница', // URL страницы
            'block_identifier' => 'Идентификатор блока', // Идентификатор блока контента
            'content' => 'Содержимое', // Содержимое блока
            'created_at' => 'Дата создания', // Дата создания записи
            'updated_at' => 'Дата обновления', // Дата обновления записи
        ];
    }

    /**
     * Метод, выполняющийся после сохранения модели
     * @param bool $insert - флаг вставки новой записи
     * @param array $changedAttributes - измененные атрибуты
     */
    public function afterSave($insert, $changedAttributes)
    {
        // Вызов родительского метода
        parent::afterSave($insert, $changedAttributes);
        
        // Формирование ключа кэша
        $cacheKey = "page_content_{$this->page_url}_{$this->block_identifier}";
        // Очистка кэша для обновленной страницы
        Yii::$app->cache->delete($cacheKey);
    }

    /**
     * Получение контента страницы с использованием кэширования
     * @param string $pageUrl - URL страницы
     * @param string $identifier - идентификатор блока
     * @return string|null - содержимое блока или null
     */
    public static function getContent($pageUrl, $identifier)
    {
        // Формирование ключа кэша
        $cacheKey = "page_content_{$pageUrl}_{$identifier}";
        
        // Отключаем кэширование для отладки
        Yii::$app->cache->delete($cacheKey);
        
        // Поиск записи в базе данных
        $model = static::findOne([
            'page_url' => $pageUrl,
            'block_identifier' => $identifier
        ]);

        // Логируем параметры поиска
        Yii::debug([
            'method' => 'getContent',
            'pageUrl' => $pageUrl,
            'identifier' => $identifier,
            'found' => !is_null($model),
            'content' => $model ? $model->content : null
        ]);

        // Возврат содержимого блока или null, если блок не найден
        return $model ? $model->content : null;
    }
}
