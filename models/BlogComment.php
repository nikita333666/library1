<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Модель комментария к статье блога
 *
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property string $content
 * @property string $created_at
 */
class BlogComment extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blog_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['post_id', 'user_id', 'content'], 'required'],
            [['post_id', 'user_id'], 'integer'],
            [['content'], 'string'],
            [['created_at'], 'safe'],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => BlogPost::class, 'targetAttribute' => ['post_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Статья',
            'user_id' => 'Пользователь',
            'content' => 'Комментарий',
            'created_at' => 'Дата создания',
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
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Получить статью
     */
    public function getPost()
    {
        return $this->hasOne(BlogPost::class, ['id' => 'post_id']);
    }

    /**
     * Получить пользователя, оставившего комментарий
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
