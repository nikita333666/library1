<?php

// Определяем пространство имен для контроллера
namespace app\controllers\admin;

// Импортируем основной класс Yii
use Yii;
// Импортируем модель настроек сайта
use app\models\SiteSettings;

// Контроллер для управления SEO настройками
class SeoController extends AdminBaseController
{
    // Действие для отображения и сохранения SEO настроек
    public function actionIndex()
    {
        // Создаем модель настроек сайта
        $model = new SiteSettings();
        // Загружаем текущие настройки
        $model->loadSettings();

        // Если данные из формы загружены и сохранены
        if ($model->load(Yii::$app->request->post()) && $model->saveSettings()) {
            // Устанавливаем сообщение об успешном обновлении
            Yii::$app->session->setFlash('success', 'SEO настройки успешно обновлены');
            // Обновляем страницу
            return $this->refresh();
        }

        // Возвращаем представление с моделью настроек
        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
