<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса ActiveRecord для работы с базой данных
use yii\db\ActiveRecord;
// Импорт класса для работы с загруженными файлами
use yii\web\UploadedFile;

/**
 * Модель книги - описание всех свойств модели
 *
 * @property int $id - Уникальный идентификатор книги
 * @property string $title - Название книги
 * @property string $seo_url - SEO-friendly URL
 * @property string $author_firstname - Имя автора
 * @property string $author_lastname - Фамилия автора
 * @property string $description - Полное описание книги
 * @property string $short_description - Краткое описание книги
 * @property string $cover_image - Путь к файлу обложки
 * @property string $pdf_file - Путь к PDF файлу книги
 * @property int $category_id - ID категории книги
 * @property int $views - Количество просмотров книги
 * @property string $created_at - Дата создания записи
 * @property string $updated_at - Дата обновления записи
 * @property string $meta_keywords - META Keywords для SEO
 * @property string $meta_description - META Description для SEO
 * @property string $image_alt - ALT текст для изображения обложки
 * @property string $img_title - TITLE текст для изображения обложки
 * @property boolean $is_hidden - Флаг скрытия книги
 */
// Класс Book, наследующий функционал ActiveRecord
class Book extends ActiveRecord
{
    // Публичное свойство для временного хранения загруженного PDF файла
    public $pdfFile;
    // Публичное свойство для временного хранения загруженной обложки
    public $coverFile;

    /**
     * Определение имени таблицы в базе данных
     */
    public static function tableName()
    {
        // Возвращает имя таблицы 'books'
        return 'books';
    }

    /**
     * Определение правил валидации для полей модели
     */
    public function rules()
    {
        // Возвращает массив правил валидации
        return [
            // Обязательные поля для заполнения
            [['title', 'author_firstname', 'author_lastname', 'category_id'], 'required'],
            // Поле category_id должно быть целым числом
            [['category_id'], 'integer'],
            // Поля description и short_description должны быть строками
            [['description', 'short_description'], 'string'],
            // Ограничение длины строковых полей до 255 символов
            [['title', 'author_firstname', 'author_lastname', 'meta_keywords', 'meta_description', 'image_alt', 'img_title', 'seo_url'], 'string', 'max' => 255],
            // Поле is_hidden должно быть булевым
            [['is_hidden'], 'boolean'],
            // Проверка существования категории
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            // Проверка уникальности SEO URL
            [['seo_url'], 'unique'],
            // Правила валидации для SEO URL
            [['seo_url'], 'match', 'pattern' => '/^[a-z0-9-]+$/', 'message' => 'SEO URL может содержать только строчные буквы, цифры и дефис'],
            // Правила валидации для загружаемых файлов
            // Правило для загрузки обложки (PNG, JPG, JPEG до 2MB)
            [['coverFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 1024 * 1024 * 2],
            // Правило для загрузки PDF (до 10MB)
            [['pdfFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf', 'maxSize' => 1024 * 1024 * 10],
            // Разрешаем безопасное присвоение путей к файлам
            [['cover_image', 'pdf_file'], 'safe'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * Определение пользовательских названий атрибутов
     */
    public function attributeLabels()
    {
        // Возвращает массив меток атрибутов
        return [
            'id' => 'ID', // Идентификатор
            'title' => 'Название книги', // Название книги
            'seo_url' => 'SEO URL', // SEO URL
            'author_firstname' => 'Имя автора', // Имя автора
            'author_lastname' => 'Фамилия автора', // Фамилия автора
            'description' => 'Полное описание', // Полное описание
            'short_description' => 'Краткое описание', // Краткое описание
            'cover_image' => 'Обложка', // Путь к обложке
            'pdf_file' => 'PDF файл', // Путь к PDF
            'category_id' => 'Категория', // Категория книги
            'views' => 'Просмотры', // Количество просмотров
            'created_at' => 'Дата создания', // Дата создания
            'updated_at' => 'Дата обновления', // Дата обновления
            'meta_keywords' => 'META Keywords', // META Keywords
            'meta_description' => 'META Description', // META Description
            'image_alt' => 'ALT текст изображения', // ALT текст
            'img_title' => 'TITLE текст изображения', // TITLE текст
            'is_hidden' => 'Скрыть книгу', // Статус скрытия
        ];
    }

    /**
     * Определение связи с моделью Category
     */
    public function getCategory()
    {
        // Возвращает связь один-к-одному с моделью Category
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Определение связи с моделью Comment
     */
    public function getComments()
    {
        // Возвращает связь один-ко-многим с моделью Comment, сортировка по дате создания
        return $this->hasMany(Comment::class, ['book_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * Определение связи с моделью Favorite
     */
    public function getFavorites()
    {
        // Возвращает связь один-ко-многим с моделью Favorite
        return $this->hasMany(Favorite::class, ['book_id' => 'id']);
    }

    /**
     * Определение связи с моделью User через Favorite
     */
    public function getFavoriteUsers()
    {
        // Возвращает связь многие-ко-многим с моделью User через таблицу favorites
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->via('favorites');
    }

    /**
     * Определение связи с моделью ViewHistory
     */
    public function getViewHistory()
    {
        // Возвращает связь один-ко-многим с моделью ViewHistory
        return $this->hasMany(ViewHistory::class, ['book_id' => 'id']);
    }

    /**
     * Определение связи с моделью User через ViewHistory
     */
    public function getViewHistoryUsers()
    {
        // Возвращает связь многие-ко-многим с моделью User через таблицу view_history
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->via('viewHistory');
    }

    /**
     * Безопасное удаление файла с проверками
     */
    public static function safeUnlink($filePath)
    {
        try {
            // Проверка существования файла
            if (!file_exists($filePath)) {
                return true;
            }
            
            // Проверка прав на запись
            if (!is_writable($filePath)) {
                Yii::warning("Файл {$filePath} недоступен для записи");
                return false;
            }

            // Очистка памяти перед удалением
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }

            // Попытка удаления файла
            return @unlink($filePath);
        } catch (\Exception $e) {
            // Логирование ошибки при удалении
            Yii::warning("Ошибка при удалении файла {$filePath}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Очистка неиспользуемых файлов
     */
    public static function cleanupUploads()
    {
        try {
            // Определение путей к директориям
            $uploadsDir = Yii::getAlias('@webroot/uploads');
            $coversDir = $uploadsDir . '/covers';
            $pdfsDir = $uploadsDir . '/pdfs';

            // Получение списка используемых файлов
            $usedCovers = self::find()->select('cover_image')->where(['not', ['cover_image' => null]])->column();
            $usedPdfs = self::find()->select('pdf_file')->where(['not', ['pdf_file' => null]])->column();

            // Очистка неиспользуемых обложек
            if (is_dir($coversDir)) {
                foreach (scandir($coversDir) as $file) {
                    if ($file !== '.' && $file !== '..' && !in_array($file, $usedCovers)) {
                        $filePath = $coversDir . '/' . $file;
                        if (is_file($filePath)) {
                            self::safeUnlink($filePath);
                        }
                    }
                }
            }

            // Очистка неиспользуемых PDF файлов
            if (is_dir($pdfsDir)) {
                foreach (scandir($pdfsDir) as $file) {
                    if ($file !== '.' && $file !== '..' && !in_array($file, $usedPdfs)) {
                        $filePath = $pdfsDir . '/' . $file;
                        if (is_file($filePath)) {
                            self::safeUnlink($filePath);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Логирование ошибки при очистке
            Yii::error("Ошибка при очистке файлов: " . $e->getMessage());
        }
    }

    /**
     * Загрузка файлов книги
     */
    public function upload()
    {
        // Проверка валидации
        if ($this->validate()) {
            try {
                // Создание необходимых директорий
                $uploadsDir = Yii::getAlias('@webroot/uploads');
                $coversDir = $uploadsDir . '/covers';
                $pdfsDir = $uploadsDir . '/pdfs';

                // Создание директорий, если они не существуют
                foreach ([$uploadsDir, $coversDir, $pdfsDir] as $dir) {
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                }

                // Обработка загрузки обложки
                if ($this->coverFile instanceof UploadedFile) {
                    // Генерация уникального имени файла
                    $fileName = 'cover_' . time() . '_' . uniqid() . '.' . $this->coverFile->extension;
                    $filePath = $coversDir . '/' . $fileName;
                    
                    // Сохранение файла
                    if ($this->coverFile->saveAs($filePath)) {
                        // Удаление старой обложки
                        if ($this->cover_image && file_exists($coversDir . '/' . $this->cover_image)) {
                            self::safeUnlink($coversDir . '/' . $this->cover_image);
                        }
                        $this->cover_image = $fileName;
                    } else {
                        $this->addError('coverFile', 'Ошибка при сохранении обложки');
                        return false;
                    }
                }

                // Обработка загрузки PDF
                if ($this->pdfFile instanceof UploadedFile) {
                    // Генерация уникального имени файла
                    $fileName = 'book_' . time() . '_' . uniqid() . '.' . $this->pdfFile->extension;
                    $filePath = $pdfsDir . '/' . $fileName;
                    
                    // Сохранение файла
                    if ($this->pdfFile->saveAs($filePath)) {
                        // Удаление старого PDF
                        if ($this->pdf_file && file_exists($pdfsDir . '/' . $this->pdf_file)) {
                            self::safeUnlink($pdfsDir . '/' . $this->pdf_file);
                        }
                        $this->pdf_file = $fileName;
                    } else {
                        $this->addError('pdfFile', 'Ошибка при сохранении PDF');
                        return false;
                    }
                }

                return true;
            } catch (\Exception $e) {
                // Логирование ошибки
                Yii::error("Ошибка при загрузке файлов: " . $e->getMessage());
                $this->addError('coverFile', 'Произошла ошибка при загрузке файлов');
                return false;
            }
        }
        return false;
    }

    /**
     * Проверка наличия книги в избранном у пользователя
     */
    public function isInFavorites($userId)
    {
        // Проверка существования записи в таблице избранного
        return Favorite::find()
            ->where(['user_id' => $userId, 'book_id' => $this->id])
            ->exists();
    }

    /**
     * Получение самых просматриваемых книг
     */
    public static function getTopViewed()
    {
        // Возврат 3 самых просматриваемых книг
        return self::find()
            ->orderBy(['views' => SORT_DESC])
            ->limit(3)
            ->all();
    }

    /**
     * Получение рекомендуемых книг
     */
    public static function getRecommended()
    {
        // Возврат следующих 3 популярных книг после топовых
        return self::find()
            ->orderBy(['views' => SORT_DESC])
            ->offset(3)
            ->limit(3)
            ->all();
    }

    /**
     * Получение последних добавленных книг
     */
    public static function getLatest()
    {
        // Возврат 3 последних добавленных книг
        return self::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(3)
            ->all();
    }

    /**
     * Получение случайных книг
     */
    public static function getRandomBooks($limit = 3)
    {
        // Возврат случайных книг в указанном количестве
        return self::find()
            ->orderBy(['RAND()' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Получение полного имени автора
     */
    public function getAuthorName()
    {
        // Конкатенация имени и фамилии автора
        return $this->author_firstname . ' ' . $this->author_lastname;
    }

    /**
     * Получение количества уникальных просмотров
     */
    public function getUniqueViewsCount()
    {
        // Подсчет уникальных просмотров через SQL-запрос
        return (int)Yii::$app->db->createCommand('
            SELECT COUNT(DISTINCT user_id) 
            FROM book_views 
            WHERE book_id = :book_id
        ', [':book_id' => $this->id])->queryScalar();
    }

    /**
     * Добавление просмотра книги
     */
    public function addView($userId)
    {
        // Проверка существования просмотра
        $exists = (bool)Yii::$app->db->createCommand('
            SELECT 1 
            FROM book_views 
            WHERE book_id = :book_id AND user_id = :user_id
            LIMIT 1
        ', [
            ':book_id' => $this->id,
            ':user_id' => $userId
        ])->queryScalar();

        // Если просмотра нет, добавляем
        if (!$exists) {
            // Добавление записи о просмотре
            Yii::$app->db->createCommand()->insert('book_views', [
                'book_id' => $this->id,
                'user_id' => $userId,
            ])->execute();

            // Увеличение счетчика просмотров
            $this->updateCounters(['views' => 1]);
            
            return true;
        }

        return false;
    }

    /**
     * Получение самых просматриваемых книг по уникальным просмотрам
     */
    public static function getMostViewed($limit = 4)
    {
        // Возврат книг, отсортированных по количеству уникальных просмотров
        return self::find()
            ->select(['books.*', 'COUNT(DISTINCT bv.user_id) as unique_views'])
            ->leftJoin('book_views bv', 'books.id = bv.book_id')
            ->groupBy('books.id')
            ->orderBy(['unique_views' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Перед сохранением модели
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Если SEO URL не задан, генерируем его из названия
            if (empty($this->seo_url)) {
                $this->seo_url = $this->generateSeoUrl($this->title);
            }
            
            // Если это новая запись, устанавливаем дату создания
            if ($this->isNewRecord) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            
            // Обновляем дату изменения
            $this->updated_at = date('Y-m-d H:i:s');
            
            return true;
        }
        return false;
    }

    /**
     * Генерирует SEO-friendly URL из заголовка
     */
    public function generateSeoUrl($title)
    {
        // Транслитерация кириллицы
        $cyr = ['а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
                'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
                'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
                'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я', ' '];
        $lat = ['a','b','v','g','d','e','yo','zh','z','i','y','k','l','m','n','o','p',
                'r','s','t','u','f','h','ts','ch','sh','sch','','y','','e','yu','ya',
                'a','b','v','g','d','e','yo','zh','z','i','y','k','l','m','n','o','p',
                'r','s','t','u','f','h','ts','ch','sh','sch','','y','','e','yu','ya', '-'];

        // Транслитерация
        $url = str_replace($cyr, $lat, mb_strtolower($title));
        
        // Замена всех не-alphanumeric символов на дефис
        $url = preg_replace('/[^a-z0-9-]/', '-', $url);
        
        // Удаление множественных дефисов
        $url = preg_replace('/-+/', '-', $url);
        
        // Удаление дефисов в начале и конце
        $url = trim($url, '-');
        
        return $url;
    }
}
