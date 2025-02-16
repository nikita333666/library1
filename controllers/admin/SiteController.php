<?php

// Определяем пространство имен для контроллера
namespace app\controllers\admin;

// Импортируем основной класс Yii
use Yii;

// Контроллер для управления сайтом в админке
class SiteController extends AdminBaseController
{
    // Действие для отображения главной страницы админки
    public function actionIndex()
    {
        // Возвращаем представление для главной страницы админки
        return $this->render('index');
    }
}
