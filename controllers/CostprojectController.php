<?php

namespace app\controllers;

use app\components\Html;
use app\dictionaries\CurrencyCodesDictEwf;
use app\models\Costproject;
use app\models\Order;
use app\models\Orderitem;
use app\models\forms\AddUserForm;
use app\models\search\CostprojectSearch;

// use rudissaar\fpdf\FPDFPlus;
use app\components\MyFPDFPlus;

use Yii;
use yii\data\ArrayDataProvider;
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
    public $fluid = false;

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
                            'actions' => ['index', 'checkout'],
                            'roles' => ['manageCostprojects'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['view', 'breakdown', 'breakdown-pdf'],
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
                                $id=null;
                                if(!empty(Yii::$app->request->get('id')))
                                    $id = Yii::$app->request->get('id');
                                elseif(!empty($_GET['AddUserForm']['costprojectId']))
                                    $id = $_GET['AddUserForm']['costprojectId'];

                                return ['costproject' => Costproject::find()->where(['id'=>$id])->one()];
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
        $this->fluid = true;
        $model = $this->findModel($id);
        // Was project payed already?
        if(is_null($model->orderId)) {
            // Check avalable deposit
            $result = Order::pay($id);
            if(!$result) {
                Yii::$app->session->addFlash('warning', 
                    Html::tag('h4', Yii::t('app', 'Payment Required') ) .
                    Yii::t('app', 'Please pay a small fee in order to view the cost breakdown.') .
                    '<br>' . Html::tag('div', Html::tag('span', 'Loading...', ['class'=>"sr-only"]), ['class' => "spinner-border text-primary", 'role' => "status"])
                );
                return $this->redirect(['checkout', 'id'=>$id]);
            } else {
                $model = $this->findModel($id);
            }
        }

        // Get expenses for grid
        $expensesDataProvider = new ArrayDataProvider([
            'allModels' => $model->expenses,
            'key' => 'id',
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('breakdown', [
            'model'                 => $model,
            'expensesDataProvider'  => $expensesDataProvider
        ]);
    }

    /**
     * Displays the cost breakdown for a single Costproject model as PDF
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBreakdownPdf($id)
    {


        $this->layout = false;
        $model = $this->findModel($id);
        // Was project payed already?
        if(is_null($model->orderId)) {
            // Check avalable deposit
            $result = Order::pay($id);
            if(!$result) {
                Yii::$app->session->addFlash('warning', 
                    Html::tag('h4', Yii::t('app', 'Payment Required') ) .
                    Yii::t('app', 'Please pay a small fee in order to view the cost breakdown.') .
                    '<br>' . Html::tag('div', Html::tag('span', 'Loading...', ['class'=>"sr-only"]), ['class' => "spinner-border text-primary", 'role' => "status"])
                );
                return $this->redirect(['checkout', 'id'=>$id]);
            } else {
                $model = $this->findModel($id);
            }
        }

        // Get expenses for grid
        $expensesDataProvider = new ArrayDataProvider([
            'allModels' => $model->expenses,
            'key' => 'id',
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $pdf = new MyFPDFPlus();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->WriteLnEncoded(10, Yii::t('app', '{title} / Cost Breakdown', ['title' => $model->title]));

        $header = ['', ''];
        $data = [];
        $data[] = [
            $model->getAttributeLabel('title'),
            $model->title
        ];
        $data[] = [
            $model->getAttributeLabel('participants'),
            $model->participants
        ];
        $data[] = [
            $model->getAttributeLabel('sortParticipants'),
            $model->sortParticipants ? '[x]' : '[-]'
        ];
        $data[] = [
            $model->getAttributeLabel('currency'),
            CurrencyCodesDictEwf::get($model->currency),
        ];
        $data[] = [
            $model->getAttributeLabel('useCurrency'),
            $model->useCurrency ? '[x]' : '[-]'
        ];
        $data[] = [
            $model->getAttributeLabel('description'),
            $model->description
        ];
        $tmp = [];
        foreach($model->users as $user) {
            $item =  $user->displayName.' (#'.$user->id.')';
            $tmp[] = $item;
        }
        $data[] = [
            Yii::t('app', 'Users'),
            join('; ', $tmp)
        ];
        $data[] = [
            $model->getAttributeLabel('orderId'),
            $model->isPaid ? Yii::t('app', 'Paid') : Yii::t('app', 'Not Paid')
        ];
        $pdf->FancyTable($header, $data, [70, 100]);
        $pdf->Ln();

        // Total Project Costs
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->WriteLnEncoded(10, Yii::t('app', 'Total Project Costs'));
        $pdf->SetFont('Arial', '', 14);
        $pdf->WriteLnEncoded(10, Yii::$app->formatter->asCurrency($model->totalExpenses, $model->currency));

        $pdf->Output('I', Yii::t('app', 'Cost Breakdown').'.pdf');
        exit;
    }

    /**
     * Shows the checkout page.
     * @return string|\yii\web\Response
     */
    public function actionCheckout($id)
    {
        $model = $this->findModel($id);
        // Was project payed already?
        if($model->isPaid) {
            Yii::$app->session->addFlash('warning', 
                Html::tag('h4', Yii::t('app', 'Payment Already Provided') ) .
                Yii::t('app', 'This cost project has already been payed.')
            );
            return $this->redirect(['view', 'id'=>$id]);
        }
        // DEBUG \yii\helpers\VarDumper::dump(Yii::$app->params, 10, true);

        // Get PayPal settings
        // 1. client id
        if(empty(Yii::$app->params['paypal.clientId'])) {
            Yii::error('App Parameter paypal.clientId is not configured', '__METHOD__');
            Yii::$app->session->addFlash(
                'error', 
                Html::tag('h4', Yii::t('app', 'Error')) .
                Yii::t('app', 'PayPal is not configured')
            );
            return $this->redirect(['view', 'id'=>$id]);
        } 
        $paypalClientID = Yii::$app->params['paypal.clientId'];

        // 2. client secret
        if(empty(Yii::$app->params['paypal.clientSecret'])) {
            Yii::error('App Parameter paypal.clientSecret is not configured', '__METHOD__');
            Yii::$app->session->addFlash(
                'error', 
                Html::tag('h4', Yii::t('app', 'Error')) .
                Yii::t('app', 'PayPal is not configured')
            );
            return $this->redirect(['view', 'id'=>$id]);
        } 
        $paypalClientSecret = Yii::$app->params['paypal.clientSecret'];

        // Get vendor currency
        if(empty(Yii::$app->params['paymentCurrencyCode'])) {
            Yii::error('App Parameter paymentCurrencyCode is not configured', __METHOD__);
            Yii::$app->session->addFlash(
                'error', 
                Html::tag('h4', Yii::t('app', 'Error')) .
                Yii::t('app', 'Payment is not configured')
            );
            return $this->redirect(['view', 'id'=>$id]);
        }
        $currencyCode = Yii::$app->params['paymentCurrencyCode'];

        // Get configured payment options
        // $paymentOptions = Yii::$app->params['paymentOptions'];
        $paymentOptions = Orderitem::find()->orderBy(['type' => SORT_ASC])->all();
        
        return $this->render('checkout', [
            'model'                 => $model,
            'paypalClientID'        => $paypalClientID,
            // 'paypalClientSecret'    => $paypalClientSecret,
            'paymentOptions'        => $paymentOptions,
            'currencyCode'          => $currencyCode,
        ]);
    }

    /**
     * Creates a new Costproject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        // Check if user has not exceeded maximum number of allwoed cost projects
        $maxNbrOfCostProjects = Yii::$app->params['user.maxNbrOfCostProjects'] ?? 1;
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
            return $this->redirect(Url::previous('cost-project'));
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
        $model->scenario = AddUserForm::SCENARIO_ADD_USER;
        $model->costprojectId = (int)$id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->validate() && $model->addUser()) {
                Yii::$app->session->addFlash('success', 
                    Html::tag('h4', Yii::t('app', 'Add User'))
                    . Yii::t('app', 'The user {username} has been added.', ['username' => $model->username])
                    . '<br>'
                    . Yii::t('app', 'An email was sent to this user containing a link to this cost project.') 
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
                    . Yii::t('app', 'The user {username} has been removed.', ['username' => $model->username])
                    . '<br>'
                    . Yii::t('app', 'An email was sent to the user to inform about the removal.')
                );
            } else {
                if($model->hasErrors()) {
                    Yii::$app->session->addFlash('error', 
                        Html::tag('h4', Yii::t('app', 'Remove User'))
                        . Yii::t('app', 'The user {username} has not been removed.', ['username' => $model->username]) . ' ' 
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
