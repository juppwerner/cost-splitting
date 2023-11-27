<?php

namespace app\controllers;

use app\models\Costproject;
use app\models\Expense;
use app\models\search\CostprojectSearch;
use app\models\search\ExpenseSearch;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\components\Html;

/**
 * ExpenseController implements the CRUD actions for Expense model.
 */
class ExpenseController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Expense models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ExpenseSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        // Get a list of all user-assigned cost projects
        $costprojects = Costproject::find()
            ->select(['costproject.*'])
            ->innerJoinWith('users')
            ->where(['user.id' => Yii::$app->user->id])
            ->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'costprojects' => $costprojects
        ]);
    }

    /**
     * Displays a single Expense model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Expense model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $lastId ID of last created expense
     * @return string|\yii\web\Response
     */
    public function actionCreate($lastId=null)
    {
        // Do we already have a user cost project?
        $searchModel = new CostprojectSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        if($dataProvider->getTotalCount()===0) {
            Yii::$app->session->addFlash('warning', Yii::t('app', 'You do not have any cost projects yet.') . '<br>' . Yii::t('app', 'Create the first cost project now.'));
            return $this->redirect(['costproject/create']);
        }

        $model = new Expense();

        $model->itemDate = date('Y-m-d');
        $model->expenseType = \app\dictionaries\ExpenseTypesDict::EXPENSETYPE_EXPENSE;
        $model->splitting = Expense::SPLITTING_EQUAL;

        // Copy some default values from previous entry?
        if(!empty($lastId)) {
            $lastModel = $this->findModel(($lastId));
            if(!empty($lastModel)) {
                $model->expenseType = $lastModel->expenseType;
                $model->itemDate = $lastModel->itemDate;
                $model->payedBy = $lastModel->payedBy;    
            }
        }

        $model->load($this->request->get());

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    Html::tag('h4', Yii::t('app', 'Create New Expense'))
                    . Html::tag('div', Yii::t('app', 'The expense <b>{title}</b> has been created.', ['title'=>$model->title])) . '<br>'
                    . Html::a(Html::icon('eye') . Yii::t('app', 'View Expense'), ['view', 'id'=>$model->id], ['class'=>'btn btn-primary btn-sm']) . ' '
                    . Html::a(Html::icon('file-text') . Yii::t('app', 'View Project'), ['costproject/view', 'id'=>$model->costprojectId], ['class'=>'btn btn-primary btn-sm']) . ' '
                    . Html::a(Html::icon('file-text') . Yii::t('app', 'View Cost Breakdown'), ['costproject/breakdown', 'id'=>$model->costprojectId], ['class'=>'btn btn-primary btn-sm'])
                );
                return $this->redirect(['create', 'Expense[costprojectId]' => $model->costprojectId, 'lastId' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        // Get a list of all user-assigned cost projects
        $costprojects = Costproject::find()
            ->select(['costproject.*'])
            ->innerJoinWith('users')
            ->where(['user.id' => Yii::$app->user->id])
            ->all();

        // Get a list of all participants, if a cost project is already selected
        $participants = null;
        if(!empty($model->costprojectId))
            $participants = Costproject::findOne(['id' => $model->costprojectId])->getParticipantsList();
        if(!is_array($model->participants)) {
            if(empty($model->participants))
                $model->participants = array();
            else
                $model->participants = explode(';', $model->participants);
        }

        // Get all titles
        $costprojectIDs = Costproject::find()
            ->select(['costproject.id'])
            ->innerJoinWith('users')
            ->where(['user.id' => Yii::$app->user->id])
            ->column();

        $titles = Yii::$app->db->createCommand(
            "SELECT e.`title` "
            . "FROM `expense` e "
            . "LEFT JOIN `costproject` cp ON e.costprojectId=cp.id "
            . "LEFT JOIN `user_costproject` uc ON cp.id=uc.costprojectId "
            . "WHERE uc.userId=:userId "
            . "GROUP BY e.title "
            . "ORDER BY e.title")
            ->bindValue(':userId', Yii::$app->user->id)
            ->queryColumn();

        return $this->render('create', [
            'model' => $model,
            'costprojects' => $costprojects,
            'participants' => $participants,
            'titles' => $titles,
        ]);
    }

    /**
     * Updates an existing Expense model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            if($model->load($this->request->post()) && $model->validate() && $model->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    Html::tag('h4', Yii::t('app', 'Update Expense'))
                    . Yii::t('app', 'The expense <em>{title}</em> has been updated.', ['title'=>$model->title]) . '<br>'
                    . Html::tag('ul', Html::tag('li', Html::a(Yii::t('app', 'View Expense'), ['expense/view', 'id'=>$model->id]) . ' | '
                    . Html::a(Yii::t('app', 'Edit'), ['expense/update', 'id'=>$model->id])))
                );
                if(!empty(Yii::$app->session->get('cost-project'))) 
                    return $this->redirect(Url::previous('cost-project'));
                else
                    return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        // Get a list of all user-assigned cost projects
        $costprojects = Costproject::find()
        ->select(['costproject.*'])
        ->innerJoinWith('users')
        ->where(['user.id' => Yii::$app->user->id])
        ->all();
   
        // Get a list of all participants, if a cost project is already selected
        $participants = null;
        if(!empty($model->costprojectId))
            $participants = Costproject::findOne(['id' => $model->costprojectId])->getParticipantsList();
        if(!is_array($model->participants)) {
            if(empty($model->participants))
                $model->participants = array();
            else
                $model->participants = explode(';', $model->participants);
        }

        // Get all titles
        $costprojectIDs = Costproject::find()
            ->select(['costproject.id'])
            ->innerJoinWith('users')
            ->where(['user.id' => Yii::$app->user->id])
            ->column();

        $titles = Yii::$app->db->createCommand(
            "SELECT e.`title` "
            . "FROM `expense` e "
            . "LEFT JOIN `costproject` cp ON e.costprojectId=cp.id "
            . "LEFT JOIN `user_costproject` uc ON cp.id=uc.costprojectId "
            . "WHERE uc.userId=:userId "
            . "GROUP BY e.title "
            . "ORDER BY e.title")
            ->bindValue(':userId', Yii::$app->user->id)
            ->queryColumn();

        return $this->render('update', [
            'model'         => $model,
            'costprojects'  => $costprojects,
            'participants'  => $participants,
            'titles'        => $titles,
        ]);
    }

    /**
     * Deletes an existing Expense model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash(
            'success',
            Html::tag('h4', Yii::t('app', 'Delete Expense'))
            . Yii::t('app', 'The expense <em>{title}</em> has been deleted.', ['title'=>$model->title])
        );

        return $this->redirect(['costproject/view', 'id'=>$model->costprojectId]);
    }

    /**
     * Finds the Expense model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Expense the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Expense::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
