<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса ActiveRecord
use yii\db\ActiveRecord;

/**
 * Класс модели настроек SEO
 * Управляет SEO настройками для страниц сайта
 *
 * @property int $id - Уникальный идентификатор записи
 * @property string $page_url - URL страницы
 * @property string $title - Заданный META заголовок
 * @property string $current_title - Текущий META заголовок
 * @property string $description - Заданное META описание
 * @property string $current_description - Текущее META описание
 * @property string $keywords - Заданные META ключевые слова
 * @property string $current_keywords - Текущие META ключевые слова
 */
class SeoSettings extends ActiveRecord
{
    /**
     * Определение имени таблицы в базе данных
     */
    public static function tableName()
    {
        // Возвращает имя таблицы seo_settings
        return 'seo_settings';
    }

    /**
     * Определение правил валидации полей
     */
    public function rules()
    {
        // Возвращает массив правил валидации
        return [
            // URL страницы обязателен для заполнения
            [['page_url'], 'required'],
            // Текстовые поля должны быть строками
            [['description', 'current_description', 'keywords', 'current_keywords'], 'string'],
            // Ограничение длины строковых полей
            [['page_url', 'title', 'current_title'], 'string', 'max' => 255],
            // URL страницы должен быть уникальным
            [['page_url'], 'unique'],
            // Текущие значения могут принимать любые безопасные значения
            [['current_title', 'current_description', 'current_keywords'], 'safe'],
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
            'title' => 'Title', // Заданный заголовок
            'current_title' => 'Текущий Title', // Текущий заголовок
            'description' => 'Description', // Заданное описание
            'current_description' => 'Текущий Description', // Текущее описание
            'keywords' => 'Keywords', // Заданные ключевые слова
            'current_keywords' => 'Текущие Keywords', // Текущие ключевые слова
        ];
    }

    /**
     * Метод получения текущих значений SEO со страницы
     * @return array|null - массив с текущими значениями или null
     */
    public function getCurrentValues()
    {
        // Создание контроллера для указанной страницы
        $controller = Yii::$app->createController($this->page_url)[0];
        // Проверка успешности создания контроллера
        if (!$controller) {
            return null;
        }

        // Создание объекта представления
        $view = new \yii\web\View();
        // Установка контекста представления
        $view->context = $controller;

        try {
            // Разбор URL на части (контроллер и действие)
            list($controllerId, $actionId) = explode('/', $this->page_url);
            // Выполнение действия контроллера
            $controller->{'action' . ucfirst($actionId)}();
            
            // Возврат текущих значений SEO
            return [
                'title' => $view->title,
                'description' => $view->params['metaDescription'] ?? '',
                'keywords' => $view->params['metaKeywords'] ?? ''
            ];
        } catch (\Exception $e) {
            // Логирование ошибки при получении значений
            Yii::error("Ошибка при получении текущих значений SEO: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Метод получения SEO настроек для указанной страницы
     * @param string $pageUrl - URL страницы
     * @return array|null - массив с настройками или null
     */
    public static function getForPage($pageUrl)
    {
        // Получение объекта кэша
        $cache = Yii::$app->cache;
        // Формирование ключа кэша
        $cacheKey = "seo_settings_{$pageUrl}";
        // Попытка получения данных из кэша
        $data = $cache->get($cacheKey);
        
        // Если данных нет в кэше
        if ($data === false) {
            // Получение данных из базы данных
            $model = self::findOne(['page_url' => $pageUrl]);
            if ($model) {
                // Формирование массива данных
                $data = [
                    'title' => $model->title,
                    'description' => $model->description,
                    'keywords' => $model->keywords
                ];
                // Сохранение данных в кэш на 1 час
                $cache->set($cacheKey, $data, 3600);
            }
        }
        
        // Возврат полученных данных
        return $data;
    }

    /**
     * Метод получения базовых ключевых слов сайта
     * @return string - строка базовых ключевых слов
     */
    public function getBaseKeywords()
    {
        // Возврат предопределенных базовых ключевых слов
        return 'библиотека, книги, чтение, литература, электронные книги';
    }

    /**
     * Метод комбинирования базовых и пользовательских ключевых слов
     * @return string - объединенная строка ключевых слов
     */
    public function getCombinedKeywords()
    {
        // Получение базовых ключевых слов
        $baseKeywords = $this->getBaseKeywords();
        // Если есть пользовательские ключевые слова, объединяем их с базовыми
        if (!empty($this->keywords)) {
            return $this->keywords . ', ' . $baseKeywords;
        }
        // Иначе возвращаем только базовые
        return $baseKeywords;
    }

    /**
     * Метод, выполняющийся перед сохранением модели
     * @param bool $insert - флаг вставки новой записи
     * @return bool - результат выполнения
     */
    public function beforeSave($insert)
    {
        // Вызов родительского метода
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Очистка кэша перед сохранением
        Yii::$app->cache->delete("seo_settings_{$this->page_url}");
        
        return true;
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
        
        // Сохранение новых значений в кэш
        Yii::$app->cache->set(
            "seo_settings_{$this->page_url}",
            [
                'title' => $this->title,
                'description' => $this->description,
                'keywords' => $this->keywords
            ],
            3600 // Кэширование на 1 час
        );
    }
}
