<?php

namespace app\controllers;

use Yii;
use app\models\BlogPost;
use app\models\BlogComment;
use app\models\BlogPostSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;

class BlogController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete', 'delete-comment'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->isAdmin();
                        }
                    ],
                    [
                        'actions' => ['delete-comment'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-comment' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        // Отключаем CSRF для запросов от CKEditor
        if ($action->id === 'upload-image') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel = new BlogPostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Увеличиваем счетчик просмотров
        $model->incrementViews();
        
        $comment = new BlogComment();

        // Загружаем комментарии с информацией о пользователях
        $comments = BlogComment::find()
            ->where(['post_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->with('user')
            ->all();

        return $this->render('view', [
            'model' => $model,
            'comment' => $comment,
            'comments' => $comments,
        ]);
    }

    public function actionAddComment($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $model = $this->findModel($id);
        $comment = new BlogComment();

        if (Yii::$app->request->isPost) {
            $comment->post_id = $id;
            $comment->user_id = Yii::$app->user->id;
            $comment->content = Yii::$app->request->post('comment');
            $comment->created_at = date('Y-m-d H:i:s');

            if ($comment->save()) {
                Yii::$app->session->setFlash('success', 'Комментарий успешно добавлен');
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при добавлении комментария');
            }
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('view', [
            'model' => $model,
            'comment' => $comment,
        ]);
    }

    public function actionCreate()
    {
        $model = new BlogPost();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->save()) {
                if ($model->imageFile) {
                    $model->imageFile->saveAs(Yii::getAlias('@webroot/uploads/blog/') . $model->image);
                }
                return $this->redirect(['view', 'id' => $model->seo_url]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // Обработка загруженного изображения
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->imageFile) {
                $oldImage = $model->image;
                $model->image = 'blog_' . time() . '.' . $model->imageFile->extension;
            }

            if ($model->save()) {
                // Если есть новое изображение, сохраняем его
                if ($model->imageFile) {
                    $model->imageFile->saveAs(Yii::getAlias('@webroot/uploads/blog/') . $model->image);
                    // Удаляем старое изображение
                    if ($oldImage && file_exists(Yii::getAlias('@webroot/uploads/blog/') . $oldImage)) {
                        unlink(Yii::getAlias('@webroot/uploads/blog/') . $oldImage);
                    }
                }
                return $this->redirect(['view', 'id' => $model->seo_url]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    public function actionDeleteComment($id)
    {
        $comment = BlogComment::findOne($id);
        if (!$comment) {
            throw new NotFoundHttpException('Комментарий не найден.');
        }

        // Проверяем права на удаление
        if (Yii::$app->user->identity->isAdmin() || $comment->user_id === Yii::$app->user->id) {
            $comment->delete();
            Yii::$app->session->setFlash('success', 'Комментарий удален');
        } else {
            Yii::$app->session->setFlash('error', 'У вас нет прав на удаление этого комментария');
        }

        return $this->redirect(['view', 'id' => $comment->post_id]);
    }

    /**
     * Загружает дополнительные комментарии для поста
     * @param integer $post_id ID поста
     * @param integer $offset Смещение для загрузки комментариев
     * @return string JSON-ответ с HTML-кодом комментариев
     */
    public function actionLoadMoreComments($post_id, $offset)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $comments = BlogComment::find()
            ->where(['post_id' => $post_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->offset($offset)
            ->limit(4)
            ->with('user')
            ->all();

        $html = '';
        foreach ($comments as $comment) {
            $html .= '<div class="comment-item mb-3 p-3 bg-white rounded shadow-sm">';
            $html .= '<div class="comment-header d-flex justify-content-between align-items-center">';
            $html .= '<div class="comment-info">';
            $html .= '<span class="comment-author font-weight-bold">';
            $html .= '<i class="fas fa-user-circle text-primary"></i> ';
            $html .= Html::encode($comment->user->username);
            $html .= '</span>';
            $html .= '<span class="comment-date text-muted ml-3">';
            $html .= '<i class="fas fa-clock"></i> ';
            $html .= Yii::$app->formatter->asRelativeTime($comment->created_at);
            $html .= '</span>';
            $html .= '</div>';
            
            if (!Yii::$app->user->isGuest && (Yii::$app->user->identity->isAdmin() || $comment->user_id === Yii::$app->user->id)) {
                $html .= '<div class="comment-actions">';
                $html .= Html::a('<i class="fas fa-trash"></i>', ['delete-comment', 'id' => $comment->id], [
                    'class' => 'btn btn-link text-danger',
                    'title' => 'Удалить комментарий',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить этот комментарий?',
                        'method' => 'post',
                    ],
                ]);
                $html .= '</div>';
            }
            
            $html .= '</div>';
            $html .= '<div class="comment-content mt-2">';
            $html .= Html::encode($comment->content);
            $html .= '</div>';
            $html .= '</div>';
        }
        
        return [
            'success' => true,
            'html' => $html,
            'nextOffset' => $offset + count($comments),
            'hasMore' => count($comments) === 4
        ];
    }

    /**
     * Загрузка изображений через CKEditor
     */
    public function actionUploadImage()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $uploadedFile = UploadedFile::getInstanceByName('upload');
        if ($uploadedFile) {
            $fileName = 'blog_content_' . time() . '.' . $uploadedFile->extension;
            $filePath = Yii::getAlias('@webroot/uploads/blog/') . $fileName;
            
            if ($uploadedFile->saveAs($filePath)) {
                return [
                    'uploaded' => 1,
                    'fileName' => $fileName,
                    'url' => Yii::$app->urlManager->createAbsoluteUrl(['/uploads/blog/' . $fileName])
                ];
            }
        }
        
        return [
            'uploaded' => 0,
            'error' => ['message' => 'Не удалось загрузить изображение.']
        ];
    }

    /**
     * Finds the BlogPost model based on its primary key value or seo_url.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID or SEO URL
     * @return BlogPost the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (is_numeric($id)) {
            $model = BlogPost::findOne($id);
        } else {
            $model = BlogPost::find()
                ->where(['seo_url' => $id])
                ->one();
        }
            
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
