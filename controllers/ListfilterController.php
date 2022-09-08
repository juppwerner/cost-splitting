<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use app\components\BaseController;

use app\models\Listfilter;
use app\models\search\ListfilterSearch;

/**
 * ListfilterController implements the CRUD actions for Listfilter model.
 */
class ListfilterController extends BaseController
{
    // {{{ Members
    public $fluid = true;
    // }}} 
    // {{{ behaviors
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'list', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'list', 'view', 'create', 'update', 'delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'clearFilterState' => \thrieu\grid\ClearFilterStateBehavior::className(),
        ];
    } // }}} 
    // {{{ actionIndex
    /**
     * Lists all Listfilter models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ListfilterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    } // }}} 
    // {{{ actionView
    /**
     * Displays a single Listfilter model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    } // }}} 
    // {{{ actionCreate
    /**
     * Creates a new Listfilter model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Listfilter();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '<h4>'.Yii::t('app', 'Create New Item').'</h4>'.Yii::t('app', 'The List Filter #{id} has been created.', [
    'id' => $model->id,
]));
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    } // }}} 
    // {{{ actionUpdate
    /**
     * Updates an existing Listfilter model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '<h4>'.Yii::t('app', 'Update Item').'</h4>'.Yii::t('app', 'The List Filter #{id} has been updated.', [
    'id' => $model->id,
]));
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    } // }}} 
    // {{{ actionApply
    public function actionApply($id)
    {
        if (($model = Listfilter::findOne($id)) !== null) {
            $state = \yii\helpers\Json::decode($model->filterState);
            // DEBUG \yii\helpers\VarDumper::dump($state, 10, true);
            $keyPrefix  = 'FilterStateBehavior';
            $route      = $model->route;
            $gridId     = '';
            $id         = $gridId !== null ? $gridId : '';
            $sessionKey = $keyPrefix . '_' . md5($route.$id);
            $session    = Yii::$app->session;
            $session->set($sessionKey, $state);
            Yii::$app->session->setFlash('success', 
                '<h4>'.Yii::t('app', 'Apply Saved Filter').'</h4>'
                .Yii::t('app', 'The saved filter <em>{name}</em> has been applied.', [ 'name'=>$model->name ] )
            );
            return $this->redirect([$model->route]); 
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested List Filter does not exist.'));
        }
    } // }}} 
    // {{{ actionDelete
    /**
     * Deletes an existing Listfilter model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', '<h4>'.Yii::t('app', 'Delete Item').'</h4>'.Yii::t('app', 'The List Filter #{id} has been deleted.', [
    'id' => $model->id,
]));
        }

        return $this->redirect(['index']);
    } // }}} 
    // {{{ findModel
    /**
     * Finds the Listfilter model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Listfilter the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Listfilter::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested List Filter does not exist.'));
        }
    } // }}} 
}
