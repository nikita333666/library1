<?php

namespace app\controllers\admin;

use Yii;
use app\models\BlogPost;
use app\models\BlogPostSearch;
use app\controllers\admin\AdminBaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * BlogController implements the CRUD actions for BlogPost model in admin panel.
 */
class BlogController extends AdminBaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all BlogPost models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BlogPostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new BlogPost model.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BlogPost();

        if ($model->load(Yii::$app->request->post())) {
            $model->author_id = Yii::$app->user->id;
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->upload() && $model->save()) {
                Yii::$app->session->setFlash('success', 'Статья успешно создана.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BlogPost model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->upload() && $model->save()) {
                Yii::$app->session->setFlash('success', 'Статья успешно обновлена.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BlogPost model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Статья успешно удалена.');

        return $this->redirect(['index']);
    }

    /**
     * Toggle status of an existing BlogPost model.
     * @param integer $id
     * @return mixed
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = !$model->status;
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Статус статьи успешно изменен.');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при изменении статуса статьи.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the BlogPost model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BlogPost the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BlogPost::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
