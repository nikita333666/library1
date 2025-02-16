<?php
// Объявление PHP кода

// Определение пространства имен для контроллера
namespace app\controllers;

// Импорт основного класса Yii
use Yii;
// Импорт базового класса Controller
use yii\web\Controller;

/**
 * Базовый контроллер
 * Предоставляет общие функции для всех контроллеров приложения
 */
class BaseController extends Controller
{
    /**
     * Метод, выполняющийся перед любым действием контроллера
     * @param \yii\base\Action $action - действие, которое будет выполнено
     * @return bool - результат выполнения метода
     */
    public function beforeAction($action)
    {
        // Выполняем стандартную проверку перед действием
        if (!parent::beforeAction($action)) {
            return false; // Если проверка не пройдена, возвращаем false
        }

        // Применяем SEO настройки к текущему действию
        Yii::$app->seo->apply();

        return true; // Возвращаем true, если все проверки пройдены
    }
}
