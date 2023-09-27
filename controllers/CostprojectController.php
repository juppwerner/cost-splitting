<?php

namespace app\controllers;

use app\components\Html;
use app\models\Costproject;
use app\models\forms\AddUserForm;
use app\models\search\CostprojectSearch;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * CostprojectController implements the CRUD actions for Costproject model.
 */
class CostprojectController extends Controller
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
                            'actions' => ['index'],
                            'roles' => ['manageCostprojects'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['view'],
                            'roles' => ['viewCostproject'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create'],
                            'roles' => ['createCostproject'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['update', 'manage-users', 'remove-user'],
                            'roles' => ['updateCostproject'],
                            'roleParams' => function() {
                                return ['costproject' => Costproject::findOne(['id' => Yii::$app->request->get('id')])];
                            },
                        ],
                        [
                            'allow' => true,
                            'actions' => ['delete'],
                            'roles' => ['deleteCostproject'],
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
     * Lists all Costproject models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CostprojectSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Costproject model.
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
     * Displays the cost breakdown for a single Costproject model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBreakdown($id)
    {
        return $this->render('breakdown', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Costproject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $maxNbrOfCostProjects = Yii::$app->params['user.maxNbrOfCostProjects'] ?? 0;
        Yii::info('maxNbrOfCostProjects: '.$maxNbrOfCostProjects, __METHOD__);
        $countUserCostProjects = Costproject::find()
        ->select(['costproject.*'])
        ->innerJoinWith('users')
        ->where(['user.id' => Yii::$app->user->id])
        ->count();
        Yii::info('countUserCostProjects: '.$countUserCostProjects, __METHOD__);
        if($countUserCostProjects + 1 > $maxNbrOfCostProjects && $maxNbrOfCostProjects>0) {
            Yii::$app->session->addFlash(
                'warning', 
                Html::tag('h4', Yii::t('app', 'Maximum Number of Cost Projects exceeded'))
                . Yii::t('app', 'You have exceeded the maximum number of allowed cost projects (limit: {n,plural,=0{no limit} =1{<b>one</b> cost project} other{<b>#</b> cost projects}}).', ['n'=>$maxNbrOfCostProjects]) . '<br>'
                . Yii::t('app', 'Currently you have {n,plural,=0{ no cost projects} =1{<b>one</b> cost project} other{<b>#</b> cost projects}}.', ['n' => $countUserCostProjects])
            );
            return $this->redirect(Url::previous());
        }
        $model = new Costproject();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // Save user <->costproject n:m relation
                Yii::$app->db->createCommand()->insert('{{user_costproject}}', [
                    'userId' => Yii::$app->user->id,
                    'costprojectId' => $model->id,
                ])->execute();
                Yii::$app->session->addFlash('success', 
                    Html::tag('h4', Yii::t('app', 'Create New Cost Project'))
                    . Html::tag('p', Yii::t('app', 'The cost project <em>{name}</em> has been created.', ['name'=>$model->recordName]))
                    . Html::a(Html::icon('plus-square') . Yii::t('app', 'Add a first expense'), ['expense/create', 'Expense[costprojectId]' => $model->id], ['class' => 'btn btn-primary btn-sm'])
                );
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Costproject model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a form to manage/add users to the project.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionManageUsers($id)
    {
        $model = new AddUserForm();
        $model->costprojectId = (int)$id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->validate() && $model->addUser()) {
                Yii::$app->session->addFlash('success', 
                    Html::tag('h4', Yii::t('app', 'Add User'))
                    . Yii::t('app', 'The user {username} has been added.', ['username' => $model->username])
                );
            }
        }

        $costproject = $this->findModel($id);

        return $this->render('manage-users', [
            'costproject' => $costproject,
            'model' => $model,
        ]);
    } 

    public function actionRemoveUser()
    {
        $model = new AddUserForm();
        if ($this->request->isPost) {
            if ($model->load($this->request->get()) && $model->validate() && $model->removeUser()) {
                Yii::$app->session->addFlash('success', 
                    Html::tag('h4', Yii::t('app', 'Remove User'))
                    . Yii::t('app', 'The user #{userId} has been removed.', ['userId' => $model->userId])
                );
            } else {
                if($model->hasErrors()) {
                    Yii::$app->session->addFlash('error', 
                        Html::tag('h4', Yii::t('app', 'Remove User'))
                        . Yii::t('app', 'The user #{userId} has not been removed.', ['userId' => $model->userId]) . ' ' 
                        . join('<br>', $model->getErrorSummary(true))
                    );
                }
            }
        }
        return $this->redirect(['manage-users', 'id'=>$model->costprojectId]);
    }

    /**
     * Deletes an existing Costproject model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = Costproject::find()
            ->select(['costproject.*'])
            ->innerJoinWith('users')
            ->where(['user.id' => Yii::$app->user->id, 'costproject.id' => $id, 'created_by'=>Yii::$app->user->id])
            ->one();
        if(!empty($model)) {
            if($model->delete()) {
                Yii::$app->session->addFlash(
                    'success', 
                    Html::tag('h4', 'Delete Cost Project')
                    . Yii::t('app', 'The cost project {name} has been deleted.', ['name'=>$model->recordName])
                );
            }
        } else {
                Yii::$app->session->addFlash(
                    'warning', 
                    Html::tag('h4', 'Delete Cost Project')
                    . Yii::t('app', 'Error!').' '
                    . Yii::t('app', 'Could not delete the cost project with ID #{id}.', ['id'=>$id])
                );
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Costproject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Costproject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Costproject::find()
            ->select(['costproject.*'])
            ->innerJoinWith('users')
            ->where(['user.id' => Yii::$app->user->id, 'costproject.id' => $id])
            ->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
