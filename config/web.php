<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'seo' => [
            'class' => 'app\components\SeoComponent',
        ],
        'request' => [
            'cookieValidationKey' => 'ZwpUWYA0oJrKPVnuPmFvGQ5JhHGjnLt8',
            'enableCsrfValidation' => false, // Временно отключаем CSRF для тестирования
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'csrfCookie' => [
                'httpOnly' => true,
                'secure' => false,
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@app/web/runtime/cache'
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'name' => 'PHPSESSID',
            'savePath' => __DIR__ . '/../runtime/sessions',
            'cookieParams' => [
                'lifetime' => 3600 * 24 * 30,
                'path' => '/',
                'domain' => '',
                'httponly' => true,
                'secure' => false,
            ],
            'timeout' => 3600 * 24 * 30,
            'useCookies' => true,
            'gcProbability' => 1,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
            'authTimeout' => 3600 * 24 * 30,
            'absoluteAuthTimeout' => 3600 * 24 * 30,
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'secure' => false,
                'path' => '/',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'admin/site-editor' => 'admin/site-editor/index',
                'admin/site-editor/edit-main' => 'admin/page-content/edit-main',
                'admin/site-editor/edit-about' => 'admin/page-content/edit-about',
                'admin/site-editor/seo' => 'admin/seo/index',
                'admin' => 'site/admin',
                'library' => 'book/index',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'htmlSanitizer' => [
            'class' => 'app\components\HtmlSanitizer',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
