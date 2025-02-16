<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Url;

/**
 * Модель статьи блога
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $image
 * @property string $seo_url
 * @property int $author_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $views
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $author_firstname
 * @property string $author_lastname
 * @property string $image_alt
 * @property string $img_title
 */
class BlogPost extends ActiveRecord
{
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blog_post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content', 'author_firstname', 'author_lastname'], 'required'],
            [['content', 'meta_description'], 'safe'],
            [['content'], 'string'],
            [['meta_description'], 'string'],
            [['author_id', 'views'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'image', 'meta_title', 'meta_keywords', 'author_firstname', 'author_lastname', 'image_alt', 'img_title', 'seo_url'], 'string', 'max' => 255],
            [['views'], 'default', 'value' => 0],
            [['author_id'], 'default', 'value' => Yii::$app->user->id],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            [['image_alt', 'img_title'], 'string', 'max' => 255],
            // Правило для SEO URL
            [['seo_url'], 'unique'],
            [['seo_url'], 'match', 'pattern' => '/^[a-z0-9-]+$/', 'message' => 'SEO URL может содержать только строчные буквы, цифры и дефис'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'seo_url' => 'SEO URL',
            'content' => 'Содержание',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'author_firstname' => 'Имя автора',
            'author_lastname' => 'Фамилия автора',
            'author_id' => 'Автор',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
            'views' => 'Просмотры',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
            'image_alt' => 'ALT текст изображения',
            'img_title' => 'TITLE текст изображения',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Получить автора статьи
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * Получить комментарии к посту
     */
    public function getComments()
    {
        return $this->hasMany(BlogComment::class, ['post_id' => 'id']);
    }

    /**
     * Загрузка изображения
     */
    public function upload()
    {
        if ($this->imageFile) {
            $fileName = 'blog_' . time() . '.' . $this->imageFile->extension;
            $path = Yii::getAlias('@webroot/uploads/blog/') . $fileName;
            if (copy($this->imageFile->tempName, $path)) {
                $this->image = $fileName;
                return true;
            }
        }
        return true; // Возвращаем true даже если нет файла
    }

    /**
     * Получить URL изображения
     */
    public function getImageUrl()
    {
        return $this->image ? Yii::getAlias('@web/uploads/blog/') . $this->image : null;
    }

    /**
     * Увеличить счетчик просмотров
     */
    public function incrementViews()
    {
        $this->views += 1;
        return $this->save(false, ['views']);
    }

    /**
     * Генерирует SEO URL из заголовка, если URL не задан
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Если seo_url пустой, генерируем его из заголовка
            if (empty($this->seo_url)) {
                $this->seo_url = $this->generateSeoUrl($this->title);
            }
            // Проверяем уникальность seo_url
            $count = self::find()
                ->where(['seo_url' => $this->seo_url])
                ->andWhere(['!=', 'id', $this->id])
                ->count();
            
            if ($count > 0) {
                // Если такой URL уже существует, добавляем к нему порядковый номер
                $this->seo_url = $this->seo_url . '-' . ($count + 1);
            }
            return true;
        }
        return false;
    }

    /**
     * Генерирует SEO-friendly URL из заголовка
     */
    protected function generateSeoUrl($title)
    {
        // Транслитерация кириллицы
        $cyr = [
            'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
            'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
            'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
            'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
        ];
        $lat = [
            'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p',
            'r','s','t','u','f','h','ts','ch','sh','sch','','y','','e','yu','ya',
            'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
            'R','S','T','U','F','H','Ts','Ch','Sh','Sch','','Y','','E','Yu','Ya'
        ];

        $title = str_replace($cyr, $lat, $title);
        // Преобразуем в нижний регистр
        $title = strtolower($title);
        // Заменяем все символы кроме букв и цифр на дефис
        $title = preg_replace('/[^a-z0-9-]+/', '-', $title);
        // Удаляем начальные и конечные дефисы
        $title = trim($title, '-');
        // Заменяем множественные дефисы одним
        $title = preg_replace('/-+/', '-', $title);

        return $title;
    }
}
