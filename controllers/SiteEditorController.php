<?php

// Определяем пространство имен для контроллера
namespace app\controllers;

// Импортируем основной класс Yii
use Yii;
// Импортируем базовый контроллер для администраторов
use app\controllers\admin\AdminBaseController;
// Импортируем модель настроек сайта
use app\models\SiteSettings;

// Контроллер для редактирования настроек сайта
class SiteEditorController extends AdminBaseController
{
    // Действие для отображения и сохранения настроек
    public function actionIndex()
    {
        // Создаем модель настроек сайта
        $model = new SiteSettings();
        // Загружаем текущие настройки
        $model->loadSettings();

        // Если данные из формы загружены и сохранены
        if ($model->load(Yii::$app->request->post()) && $model->saveSettings()) {
            // Устанавливаем сообщение об успешном сохранении
            Yii::$app->session->setFlash('success', 'Настройки сайта успешно сохранены');
            // Обновляем страницу
            return $this->refresh();
        }

        // Возвращаем представление с моделью настроек
        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
