<?php

namespace app\components;

use Yii;
use yii\base\Component;
use app\models\SeoSettings;

class SeoComponent extends Component
{
    private $_settings = [];

    /**
     * Применяет SEO настройки для текущей страницы
     */
    public function apply()
    {
        // Получаем текущий маршрут
        $route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        
        Yii::debug("Applying SEO settings for route: " . $route);
        
        // Всегда получаем актуальные данные из БД
        $model = SeoSettings::findOne(['page_url' => $route]);
        
        if ($model) {
            $settings = [
                'title' => $model->title,
                'description' => $model->description,
                'keywords' => $model->keywords
            ];
            
            // Сохраняем настройки
            $this->_settings = $settings;
            
            // Удаляем существующие мета-теги
            $view = Yii::$app->view;
            $view->metaTags = [];
            $view->title = null;
            
            // Применяем настройки
            $view->title = $settings['title'];
            
            // Мета-тег description
            $view->registerMetaTag([
                'name' => 'description',
                'content' => $settings['description']
            ], 'description');

            // Мета-тег keywords
            $view->registerMetaTag([
                'name' => 'keywords',
                'content' => $settings['keywords']
            ], 'keywords');

            // Open Graph теги для лучшего отображения в соцсетях
            $view->registerMetaTag([
                'property' => 'og:title',
                'content' => $settings['title']
            ], 'og:title');

            $view->registerMetaTag([
                'property' => 'og:description',
                'content' => $settings['description']
            ], 'og:description');

            $view->registerMetaTag([
                'property' => 'og:type',
                'content' => 'website'
            ], 'og:type');
            
            Yii::debug("Applied SEO settings: " . print_r($settings, true));
        } else {
            Yii::debug("No SEO settings found for route: " . $route);
        }
    }

    /**
     * Возвращает текущие SEO настройки
     */
    public function getSettings()
    {
        return $this->_settings;
    }
}
