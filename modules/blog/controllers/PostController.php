<?php

namespace app\modules\blog\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\modules\blog\models\Post;
use app\modules\blog\models\search\PostSearch;
use app\components\BaseController;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends BaseController
{
    // {{{ Members
    public $fluid = true;
    // }}} 
    // {{{ behaviors
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                // Only apply ACF to these actions:
                'only' => ['admin', 'index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['admin', 'index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['admin', 'create', 'update', 'delete'],
                        'roles' => ['blogAuthor'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            // For Clear Filters:
            // 'clearFilterState' => \thrieu\grid\ClearFilterStateBehavior::className(),
        ];
    } // }}} 
    // {{{ actionAdmin
    /**
     * Manage all Post models.
     * @return mixed
     */
    public function actionAdmin()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    } // }}} 
    // {{{ actionIndex
    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    } // }}} 
    // {{{ actionView
    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $this->fluid = false;
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    } // }}} 
    // {{{ actionCreate
    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->fluid = false;
        $model = new Post();

        // Defaults via GET supplied?
        if(isset($_GET['Post'])) {
            $model->load(Yii::$app->request->get());
            // $model->validate();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(
                'success', 
                '<h4>'.Yii::t('app', 'Create New Post').'</h4>'
                .Yii::t('app', 'The post {recordName} has been created.', [
                    'recordName' => $model->recordName,
            ]));
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    } // }}}
    // {{{ actionUpdate
    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->fluid = false;
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(
                'success', 
                '<h4>'.Yii::t('app', 'Update Post').'</h4>'
                .Yii::t('app', 'The post {recordName} has been updated.', [
                    'recordName' => $model->recordName,
            ]));
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    } // }}} 
    // {{{ actionDelete
    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->delete()) {
            Yii::$app->session->setFlash(
                'success', 
                '<h4>'.Yii::t('app', 'Delete Post').'</h4>'
                .Yii::t('app', 'The post {recordName} has been deleted.', [
                    'recordName' => $model->recordName,
            ]));
        }
        return $this->redirect(['index']);
    } // }}} 
    // {{{ findModel
    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    } // }}} 
}
