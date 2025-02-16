<?php

// Определяем пространство имен для контроллера
namespace app\controllers\admin;

// Импортируем основной класс Yii
use Yii;

// Контроллер для страницы "О нас" в админке
class AboutController extends AdminBaseController
{
    // Действие для отображения страницы "О нас"
    public function actionIndex()
    {
        // Возвращаем представление для страницы "О нас"
        return $this->render('index');
    }
}
