<?php
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/* @var $model app\models\BlogPost */
?>

<div class="blog-post-card">
<a href="<?= Url::to(['blog/view', 'id' => $model->seo_url]) ?>" class="post-link" style="text-decoration: none; color: inherit;">
    <div class="post-image">
        <?php if ($model->image): ?>
            <img src="<?= $model->getImageUrl() ?>" 
                 alt="<?= Html::encode($model->image_alt) ?>"
                 title="<?= Html::encode($model->img_title) ?>"
                 class="img-fluid">
        <?php else: ?>
            <div class="no-image" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; ">
                <i class="fas fa-newspaper"></i>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="post-content">
        <h2 class="post-title">
            <?= Html::encode($model->title) ?>
        </h2>
        <div class="post-meta">
            <span class="post-date">
                <i class="far fa-calendar-alt"></i>
                <?= Yii::$app->formatter->asDate($model->created_at, 'php:M d, Y') ?>
            </span>
            <span class="post-author">
                <i class="far fa-user"></i>
                <?= Html::encode($model->author_firstname . ' ' . $model->author_lastname) ?>
            </span>
        </div>
        <div class="post-excerpt">
            <?= \yii\helpers\StringHelper::truncateWords(strip_tags($model->content, '<p><span><div><strong><em><u><s><mark><sub><sup>'), 20) ?>
        </div>

        <div class="post-actions">
            <?= Html::a('ЧИТАТЬ ДАЛЕЕ', ['view', 'id' => $model->seo_url], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    </a>
</div>

<style>
.blog-post-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    max-width: 100%;
}

.post-image {
    position: relative;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
    overflow: hidden;
}

.post-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.post-content {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    max-width: 100%;
}

.post-title {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #333;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.3;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    max-width: 100%;
}

.post-meta {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 15px;
}

.post-meta span {
    margin-right: 15px;
}

.post-meta i {
    margin-right: 5px;
}

.post-excerpt {
    color: #666;
    margin-bottom: 20px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    line-height: 1.5;
    word-wrap: break-word;
    word-break: break-word;
    max-width: 100%;
    hyphens: auto;
}

.post-actions {
    margin-top: auto;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    padding: 8px 20px;
    font-weight: 500;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
</style>
