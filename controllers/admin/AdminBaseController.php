<?php

// Определяем пространство имен для контроллера
namespace app\controllers\admin;
use Yii;
// Импортируем базовый класс контроллера
use yii\web\Controller;
// Импортируем фильтр доступа
use yii\filters\AccessControl;

/**
 * Базовый контроллер для админской части
 */
class AdminBaseController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // Возвращаем массив конфигураций поведения
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true, // Разрешить доступ
                        'roles' => ['@'], // Только для авторизованных пользователей
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    return $this->redirect(['/site/index']);
                }
            ],
        ];
    }
}
