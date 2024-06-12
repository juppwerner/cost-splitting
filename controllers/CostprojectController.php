<?php

namespace app\controllers;

use app\components\Html;
use app\dictionaries\CurrencyCodesDictEwf;
use app\dictionaries\ExpenseTypesDict;
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
use yii\helpers\VarDumper as VD;

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


        $showParticipants = false;

        $totalProjectCost = 0; 
        $participantDetails = []; 
        $participantSums = []; 
        $sum = 0; 
        $participants = array_values($model->participantsList);
        $defaultParticipantDetails = [
            'sumExpenses'           => 0, 
            'countExpenses'         => 0, 
            'sumExpensesSelf'       => 0, 
            'sumExpensesOthers'     => 0, 
            'totalProjectValue'     => 0, 
            'countExpensesByOthers' => 0, 
            'sumExpensesByOthers'   => 0
        ];

        $pdf = new MyFPDFPlus();
        // Set document metadata
        $pdf->SetCreator(Yii::$app->name.' V'.Yii::$app->version);
        $pdf->SetAuthor(Yii::$app->user->identity->profile->name);
        $pdf->SetTitle($model->title);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->WriteLnEncoded(10, Yii::t('app', '{title} / Cost Breakdown', ['title' => $model->title]));

        // Table with project metadata
        $pdf->SetFont('Arial', '', 14);
        $pdf->WriteLnEncoded(10, $model->description);
        $pdf->SetWidths([70, 100]);
        $pdf->SetFillColor(224,235,255);
        $pdf->row([
            $model->getAttributeLabel('participants'),
            $model->participants
        ]);
        $pdf->row([
            $model->getAttributeLabel('sortParticipants'),
            $model->sortParticipants ? '[x]' : '[-]'
        ]);
        $pdf->row([
            $model->getAttributeLabel('currency'),
            CurrencyCodesDictEwf::get($model->currency),
        ]);
        $pdf->row([
            $model->getAttributeLabel('useCurrency'),
            $model->useCurrency ? '[x]' : '[-]'
        ]);
        $pdf->Ln();

        // Total Project Costs
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->WriteLnEncoded(10, Yii::t('app', 'Total Project Costs'));
        $pdf->SetFont('Arial', '', 14);
        $pdf->WriteLnEncoded(10, Yii::$app->formatter->asCurrency($model->totalExpenses, $model->currency));

        // Expenses Table
        $pdf->AddPage('L');
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->WriteLnEncoded(10, Yii::t('app', 'Expenses'));
        
        $headerRow = [
            Yii::t('app', 'Date'),
            Yii::t('app', 'Title'),
            Yii::t('app', 'Payed By'), 
        ];
        $dataRows = [];
        $widths     = [20, 30, 20];
        $alignsH    =  ['L', 'L', 'L'];
        $aligns     = ['L', 'L', 'L'];
        if($showParticipants) {
            $headerRow[] = Yii::t('app', 'Recipients');
            $widths[] = 20;
            $aligns[] = 'L';
        }
        $headerRow[] = Yii::t('app', 'Amount');
        $widths[] = 20;
        $alignsH[] = 'L';
        $aligns[] = 'R';
        if($model->useCurrency) {
            $headerRow[] = Yii::t('app', 'Exchange Rate');
            $headerRow[] = Yii::t('app', 'Amount {currency}', ['currency'=>$model->currency]);
            $widths[] = 20;
            $alignsH[] = 'L';
            $aligns[] = 'R';
            $widths[] = 20;
            $alignsH[] = 'L';
            $aligns[] = 'C';
        }
        $numBaseColumns = count($widths);
        // BIS HIER BASISTABELLE

        foreach($participants as $participant) {
            if(!array_key_exists($participant, $participantSums)) $participantSums[$participant] = 0;
            $headerRow[] = $participant;
            $headerRow[] = '';
            $widths[] = 20;
            $alignsH[] = 'L';
            $aligns[] = 'R';
            $widths[] = 20;
            $alignsH[] = 'L';
            $aligns[] = 'R';
        }
        $pdf->SetWidths($widths);
        $pdf->SetAligns($alignsH);
        $dataRows[] = $headerRow;
        // $pdf->Row($headerRow);

        $pdf->SetAligns($aligns);
        $breakdown = $model->getBreakdown();
        foreach($breakdown as $row) {
            $data = [];
            if(!array_key_exists($row->payedBy, $participantDetails))
                $participantDetails[$row->payedBy] = $defaultParticipantDetails;
            $data[] = Yii::$app->formatter->asDate($row->itemDate, 'php:'.Yii::t('app', 'Y-m-d')) ;
            $data[] = $row->title;
            $data[] = $row->payedBy;
            if($showParticipants) {
                $data[] = $row->splitting==Expense::SPLITTING_EQUAL ? join(', ', $model->participantsList) : str_replace(';', ', ', $row->participants);
            }
            $data[] = Yii::$app->formatter->asCurrency($row->amount, $row->currency);
            if($model->useCurrency) {
                $data[] = $row->exchangeRate;
                $data[] = Yii::$app->formatter->asCurrency($row->amount * $row->exchangeRate, $model->currency);
            }
            foreach($participants as $participant)  {
                if(!array_key_exists($participant, $participantDetails))
                    $participantDetails[$participant] = $defaultParticipantDetails;
                if($participant==$row->payedBy) {
                    $data[] = Yii::$app->formatter->asDecimal($row->amount  * $row->exchangeRate, 2);
                    $participantSums[$participant] += $row->amount  * $row->exchangeRate;
                    if($row->expenseType !== ExpenseTypesDict::EXPENSETYPE_TRANSFER) {
                        $participantDetails[$participant]['sumExpenses'] += $row->amount  * $row->exchangeRate; $participantDetails[$participant]['countExpenses']++;
                    }
                } else {
                    $data[] = '';
                }
                foreach($row->costitems as $costitem) {
                    if($costitem->participant==$participant) {
                        $data[] = Yii::$app->formatter->asDecimal($costitem->amount * $costitem->exchangeRate, 2);
                        $participantSums[$participant] -= $costitem->amount * $costitem->exchangeRate;
                        if($row->expenseType !== ExpenseTypesDict::EXPENSETYPE_TRANSFER) {
                            $totalProjectCost += $costitem->amount * $costitem->exchangeRate;
                            if($row->payedBy===$participant) {
                                $participantDetails[$participant]['sumExpensesSelf'] += $costitem->amount * $costitem->exchangeRate;
                            } else {
                                $participantDetails[$participant]['sumExpensesByOthers'] += $costitem->amount * $costitem->exchangeRate;
                                $participantDetails[$participant]['countExpensesByOthers'] ++;
                            }
                            $participantDetails[$participant]['totalProjectValue'] += $costitem->amount * $costitem->exchangeRate;
                    } else {
                        $data[] = '';
                        if($row->payedBy===$participant and $row->expenseType !== ExpenseTypesDict::EXPENSETYPE_TRANSFER) {}
                            $participantDetails[$row->payedBy]['sumExpensesOthers'] += $costitem->amount * $costitem->exchangeRate;
                        }
                    }
                } // costitems loop
            } // participants col. loop
            $delta = count($widths) - count($data);
            for($i=1; $i <= $delta; $i++)
                $data[] = '';
            $dataRows[] = $data;
            // $pdf->Row($data);
        } // breakdown rows loop

        // Participants Sums Row
        $data = [];
        for($i=1; $i<= (($showParticipants ? 5 : 4 ) + (int)$model->useCurrency*2); $i++)
            $data[] = '';
        foreach($participants as $participant) {
            $sum += $participantSums[$participant];
            $data[] = '';
            $data[] = Yii::$app->formatter->asDecimal($participantSums[$participant], 2);
        }
        $dataRows[] = $data;
        // $pdf->Row($data);

        // Repeat header row
        $pdf->SetAligns($alignsH);
        $dataRows[] = $headerRow;
        // $pdf->Row($headerRow);

        // Print table in chunks of 4 participants

        for($i=0; $i<=floor(count($participants)/4); $i++) {
            $pdf->SetFont('Arial', '', 12);
            $pdf->WriteLnEncoded(10, Yii::t('app', 'Table {0} of {1}', [($i+1), round(count($participants)/4)]));
            $idx = range(0, $numBaseColumns-1);
            $i2 = $numBaseColumns+$i*4*2;
            for($j=0;$j<4; $j++) { 
                if(isset($participants[$i*4+$j])) {
                    $idx[] = $i2++;
                    $idx[] = $i2++;
                }
            }

            $aligns2 = [];
            $alignsH2 = [];
            foreach($idx as $i3) {
                $aligns2[] = $aligns[$i3]; 
                $alignsH2[] = $alignsH[$i3]; 
            }
            for($l=0; $l<count($dataRows); $l++) {
                $dataRow2 = [];
                foreach($idx as $i3) {
                    $dataRow2[] = $dataRows[$l][$i3]; 
                }
                if($l==0 || $l==count($dataRows)-1)
                    $pdf->setAligns($alignsH2);
                else
                    $pdf->setAligns($aligns2);

                $pdf->SetFont('Arial', '', 8);
                $pdf->Row($dataRow2);
            }
            if($i<floor(count($participants)/4))
                $pdf->AddPage('L');
        }
        $pdf->Ln();
        // END OF EXPENSES TABLE


        // Summary by Participants 
        $pdf->AddPage('P');
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->WriteLnEncoded(10, Yii::t('app', 'Participants'));

        /**
         * Calculate participants balances, final money transfers
         */
        $headers            = [];
        $bilanzen           = [];
        $schlusszahlungen   = [];
        $personenKonten     = [];
        foreach($breakdown as $expense)
        {
            // VD::dump($row, 10, true);
            if(!array_key_exists($expense->payedBy, $personenKonten))
                $personenKonten[$expense->payedBy] = 0;
            foreach($expense->costitems as $costitem) {
                // VD::dump($costitem->attributes, 10, true);
                if(!array_key_exists($costitem->participant, $personenKonten))
                    $personenKonten[$costitem->participant] = 0;
                if($expense->payedBy<>$costitem->participant) {
                    $partners = [$expense->payedBy, $costitem->participant];
                    sort($partners);
                    $bilanzKey = join('|', $partners);
                    if(!array_key_exists($bilanzKey, $bilanzen)) {
                        $bilanzen[$bilanzKey] = [];
                        foreach($partners as $partner)
                            $bilanzen[$bilanzKey][$partner] = 0;
                    }
                    $bilanzen[$bilanzKey][$expense->payedBy] += $costitem->amount / $costitem->exchangeRate;
                }
            }
        }
        // DEBUG echo Html::tag('h3', 'bilanzen');
        // DEBUG VD::dump($bilanzen, 10, true);

        $numBilanzenGt0 = 0;
        foreach($bilanzen as $bilanzKey=>$partnerStaende) {
            $partners = explode('|', $bilanzKey);
            $saldo = $partnerStaende[$partners[0]] - $partnerStaende[$partners[1]];
            if(abs($saldo)<0.009)
                continue;
            // DEBUG            echo $bilanzKey.': '.$saldo.'<br>';
            if($saldo>0) {
                $schlusszahlungen[$bilanzKey] = Yii::t('app', '{participantLeft} owes {participantRight} {amount}', ['participantLeft' => $partners[1], 'participantRight' => $partners[0], 'amount' => Yii::$app->formatter->asCurrency($saldo, $model->currency)]);
                $personenKonten[$partners[0]] += round($saldo, 2);
                $personenKonten[$partners[1]] -= round($saldo, 2);
            } elseif($saldo<0) {
                $schlusszahlungen[$bilanzKey] = Yii::t('app', '{participantLeft} owes {participantRight} {amount}', ['participantLeft' => $partners[0], 'participantRight' => $partners[1], 'amount' => Yii::$app->formatter->asCurrency(abs($saldo), $model->currency)]);
                $personenKonten[$partners[0]] -= abs(round($saldo,2));
                $personenKonten[$partners[1]] += abs(round($saldo, 2));
            }
        }
        sort($schlusszahlungen);
        ksort($personenKonten);
        $amounts = array_values($personenKonten);
        foreach($personenKonten as $partner=>$saldo) {
            if($saldo !==0)
                $numBilanzenGt0++;
        }

        $persons = $participants; 
        if($model->sortParticipants)
            sort($persons);
        foreach($persons as $person) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->WriteLnEncoded(10, $person);

            $pdf->SetFont('Arial', '', 12);
            $pdf->WriteLnEncoded(6, 
                Yii::t('app', '{person} has payed {countExpenses} expenses with the total value of {sumExpenses} ({sumExpensesSelf} for himself, and {sumExpensesOthers} for others).', [
                    'person'=>$person, 
                    'countExpenses'=>$participantDetails[$person]['countExpenses'], 
                    'sumExpenses'=>Yii::$app->formatter->asCurrency($participantDetails[$person]['sumExpenses'], $model->currency),
                    'sumExpensesSelf'=>Yii::$app->formatter->asCurrency($participantDetails[$person]['sumExpensesSelf'], $model->currency),
                    'sumExpensesOthers'=>Yii::$app->formatter->asCurrency($participantDetails[$person]['sumExpensesOthers'], $model->currency),
                ])
                . ' ' . Yii::t('app', 'Other participants have payed {countExpensesByOthers}x for {person}.', [
                    'person'=>$person, 
                    'countExpensesByOthers'=>$participantDetails[$person]['countExpensesByOthers'], 
                    'sumExpensesByOthers'=>Yii::$app->formatter->asCurrency($participantDetails[$person]['sumExpensesByOthers'], $model->currency)
                ])
            );
            if($personenKonten[$person]<0) {
                $pdf->WriteLnEncoded(6, Yii::t('app', '{person} has, after billing of all payments and money transfers, debts with th eamount of {saldo}.', [
                    'person' => $person,
                    'saldo' => Yii::$app->formatter->asCurrency(abs($saldo), $model->currency)
                ]));
            } else {
                $pdf->WriteLnEncoded(6, Yii::t('app', '{person} has, after billing of all payments and money transfers, currently no debts.', [
                    'person' => $person,
                ]));
            }
        }

        /**
         * Simplified Final Money Transfers
         */
        $schlusszahlungen2  = [];
        $empfaenger         = '';
        foreach($personenKonten as $person=>$saldo) {
            // DEBUG echo $person . ' Saldo: '.$saldo;
            // if($saldo - round($saldo, 5)<0.0001) continue;
            if($saldo>0) {
                $empfaenger = $person;
            } elseif($saldo<0) {
                $schlusszahlungen2[] = [
                    'amount'=>abs($saldo), 
                    'person'=>$person, 
                    'text'=> Yii::t('app', '{person} owes __recipient__ {amount}', [
                        'person' => $person, 
                        'amount' => Yii::$app->formatter->asCurrency(abs($saldo), $model->currency)
                    ])
                ];
            }
        }
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->WriteLnEncoded(10, Yii::t('app', 'Compensation Payments'));
        $pdf->SetFont('Arial', '', 12);
        if(count($schlusszahlungen2)===0) {
            $pdf->WriteLnEncoded(6, Yii::t('app', 'You are balanced!'));
            $pdf->WriteLnEncoded(6, Yii::t('app', 'Nobody owes somebody some money.'));
        } else {
            foreach($schlusszahlungen2 as $schlusszahlung) {
                $pdf->WriteLnEncoded(6, str_replace('__recipient__', $empfaenger, $schlusszahlung['text']));
            }
        }

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
