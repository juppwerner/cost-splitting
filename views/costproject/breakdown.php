<?php

use yii\helpers\Url;
use yii\helpers\VarDumper as VD;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

use app\components\Html;
use app\dictionaries\CurrencyCodesDictEwf;
use app\dictionaries\ExpenseTypesDict;
use app\models\Expense;
use app\widgets\GridView;
use app\components\chartjs\Chart;
use app\assets\ChartJSAsset;

/** @var yii\web\View $this */
/** @var app\models\Costproject $model */
/** @var yii\data\ArrayDataProvider $expensesDataProvider Expenses for gridview */

$this->title = Yii::t('app', '{title} / Cost Breakdown', ['title' => $model->title]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

Url::remember('', 'cost-project');
YiiAsset::register($this);
ChartJSAsset::register($this);

$defaultParticipantDetails = [
    'sumExpenses'=>0, 
    'countExpenses'=>0, 
    'sumExpensesSelf'=>0, 
    'sumExpensesOthers'=>0, 
    'totalProjectValue'=>0, 
    'countExpensesByOthers'=>0, 
    'sumExpensesByOthers'=>0
];
?>
<div class="costproject-breakdown">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Html::icon('eye') . Yii::t('app', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-primary d-print-none']) ?>
        <?= Html::a(Html::icon('edit') . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary d-print-none']) ?>
    </p>

    <!-- Cost Project Detail View -->
    <?= $this->render('_view', ['model' => $model]) ?>
    <?= $this->render('_currencyNotes') ?>


    <h3><?= Yii::t('app', 'Expenses') ?></h3>
    <?php $breakdown = $model->getBreakdown(); ?>

    <?php $totalProjectCost = 0; $participantDetails = []; $participantSums = []; $sum = 0; $participants = array_values($model->participantsList); ?>

    <?php if(count($breakdown)===0) : ?>
    <div class="mt-2 mb-3"><?= Yii::t('app', '(no expenses found)') ?></div>

    <?php else : ?>

    <table class="table table-striped table-responsive-sm table-hover" style="width:100%">

        <thead>
            <tr>
                <th><?= Yii::t('app', 'Date') ?></th>
                <th><?= Yii::t('app', 'Title') ?></th>
                <th><?= Yii::t('app', 'Amount') ?></th>
                <?php if($model->useCurrency) : ?>
                <th><?= Yii::t('app', 'Exchange Rate') ?></th>
                <?php endif; ?>
                <th><?= Yii::t('app', 'Payed By') ?></th>
                <th><?= Yii::t('app', 'Recipients') ?></th>
                <?php if($model->useCurrency) : ?>
                <th><?= Yii::t('app', 'Amount {currency}', ['currency'=>$model->currency]) ?></th>
                <?php endif; ?>
                <!-- <th><?= Yii::t('app', 'Created At') ?></th>-->
                <?php foreach($participants as $participant) : ?>
                <?php if(!array_key_exists($participant, $participantSums)) $participantSums[$participant] = 0; ?>
                <th colspan="2"><?= $participant ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach($breakdown as $row) : ?>
            <?php if(!array_key_exists($row->payedBy, $participantDetails)) $participantDetails[$row->payedBy] = $defaultParticipantDetails; ?>
            <tr>
                <td class="text-center">
                    <?= Yii::$app->formatter->asDate($row->itemDate, 'php:'.Yii::t('app', 'Y-m-d')) ?>
                </td>
                <td>
                    <?= $row->title ?> <?= Html::a('.', ['/expense/update', 'id'=>$row->id], ['class'=>'d-print-none']) ?>
                </td>
                <td class="text-right">
                    <?= Yii::$app->formatter->asCurrency($row->amount, $row->currency) ?>
                </td>
                <?php if($model->useCurrency) : ?>
                <td class="text-right">
                    <?= $row->exchangeRate ?>
                </td>
                <?php endif; ?>
                <td class="text-center">
                    <?= $row->payedBy ?>
                </td>
                <td class="text-center">
                    <?= $row->splitting==Expense::SPLITTING_EQUAL ? join(', ', $model->participantsList) : $row->participants ?>
                </td>
                
                <?php if($model->useCurrency) : ?>
                <td class="text-right">
                    <?= Yii::$app->formatter->asCurrency($row->amount * $row->exchangeRate, $model->currency) ?>
                </td>
                <?php endif; ?>
                <!-- <td class="text-center"><?= Yii::$app->formatter->asDate($row->created_at, 'php:'.Yii::t('app', 'Y-m-d')) ?></td> -->
                <?php foreach($participants as $participant) : ?>
                <?php if(!array_key_exists($participant, $participantDetails)) $participantDetails[$participant] = $defaultParticipantDetails; ?>
                <td style="color:lightgreen" class="text-right">
                    <?php if($participant==$row->payedBy) : ?>
                    <?= Yii::$app->formatter->asDecimal($row->amount  * $row->exchangeRate, 2) ?>
                    <?php $participantSums[$participant] += $row->amount  * $row->exchangeRate; ?>
                    <?php if($row->expenseType !== ExpenseTypesDict::EXPENSETYPE_TRANSFER) : ?>
                        <?php $participantDetails[$participant]['sumExpenses'] += $row->amount  * $row->exchangeRate; $participantDetails[$participant]['countExpenses']++; ?>
                    <?php endif; // !money transfer ?>
                    <?php endif; // part. == payedBy ?>
                </td>
                <td style="color: red" class="text-right">
                    <?php foreach($row->costitems as $costitem) : ?>
                    <?php if($costitem->participant==$participant) : ?>
                        <?= Yii::$app->formatter->asDecimal($costitem->amount * $costitem->exchangeRate, 2) ?>
                        <?php $participantSums[$participant] -= $costitem->amount * $costitem->exchangeRate; ?>
                        <?php if($row->expenseType !== ExpenseTypesDict::EXPENSETYPE_TRANSFER) : ?>
                            <?php $totalProjectCost += $costitem->amount * $costitem->exchangeRate; ?>
                            <?php if($row->payedBy===$participant) : ?>
                                <?php $participantDetails[$participant]['sumExpensesSelf'] += $costitem->amount * $costitem->exchangeRate; ?>
                            <?php else : ?>
                                <?php $participantDetails[$participant]['sumExpensesByOthers'] += $costitem->amount * $costitem->exchangeRate; ?>
                                <?php $participantDetails[$participant]['countExpensesByOthers'] ++; ?>
                            <?php endif; ?>
                            <?php $participantDetails[$participant]['totalProjectValue'] += $costitem->amount * $costitem->exchangeRate; ?>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php if($row->payedBy===$participant and $row->expenseType !== ExpenseTypesDict::EXPENSETYPE_TRANSFER) : ?>
                            <?php $participantDetails[$row->payedBy]['sumExpensesOthers'] += $costitem->amount * $costitem->exchangeRate; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php endforeach; // costitems loop ?>
                </td>
                <?php endforeach; // participants col. loop ?>
            </tr>
            <?php endforeach; // breakdown rows loop ?>

            <!-- Participants Sums Row -->
            <tr>
                <td colspan="<?= 5 + (int)$model->useCurrency ?>">&nbsp;</td>
                <?php foreach($participants as $participant) : ?>
                <?php $sum += $participantSums[$participant]; ?>
                <td colspan="2" class="text-right"><?= Yii::$app->formatter->asCurrency($participantSums[$participant], 'EUR') ?></td>
                <?php endforeach; ?>
            </tr>

        </tbody>
    </table>

    <?php endif; ?>

    <!-- Total Project Costs Box -->
    <div class="card border-primary mb-3" style="max-width: 18rem;page-break-after: always;">
        <div class="card-header font-weight-bold"><?= Yii::t('app', 'Total Project Costs') ?></div>
        <div class="card-body text-primary">
            <h5 class="card-title text-center"><?= Yii::$app->formatter->asCurrency($model->totalExpenses, $model->currency) ?></h5>
        </div>
    </div>

    <?php
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
    ?>
    <?php // if(true or count($bilanzen)>0) : ?>

    <?php
    $numBilanzenGt0 = 0;
    foreach($bilanzen as $bilanzKey=>$partnerStaende) {
        $partners = explode('|', $bilanzKey);
        $saldo = $partnerStaende[$partners[0]] - $partnerStaende[$partners[1]];
        // echo $bilanzKey.': '.$saldo.'<br>';
        if($saldo>0) {
            $schlusszahlungen[$bilanzKey] = Yii::t('app', '{participantLeft} owes {participantRight} {amount}', ['participantLeft' => $partners[1], 'participantRight' => $partners[0], 'amount' => Yii::$app->formatter->asCurrency($saldo, $model->currency)]);
            $personenKonten[$partners[0]] += $saldo;
            $personenKonten[$partners[1]] -= $saldo;
        } elseif($saldo<0) {
            $schlusszahlungen[$bilanzKey] = Yii::t('app', '{participantLeft} owes {participantRight} {amount}', ['participantLeft' => $partners[0], 'participantRight' => $partners[1], 'amount' => Yii::$app->formatter->asCurrency($saldo, $model->currency)]);
            $personenKonten[$partners[0]] -= abs($saldo);
            $personenKonten[$partners[1]] += abs($saldo);
        }
    }
    sort($schlusszahlungen);
    ksort($personenKonten);
    $amounts = array_values($personenKonten);
    foreach($personenKonten as $partner=>$saldo) {
        if($saldo !==0)
            $numBilanzenGt0++;
    }
    ?>
    <?php if($numBilanzenGt0>0) : ?>
    <h3><?= Yii::t('app', 'Balance Sheet') ?></h3>
    <?= Chart::widget([
        'type' => Chart::TYPE_BAR,
        'datasets' => [
            [
                'data' => $personenKonten
            ],
        ],
        'clientOptions' => [
            'responsive' => true,
            'indexAxis' => 'y',
            // Elements options apply to all of the options unless overridden in a dataset
            // In this case, we are setting the border of each horizontal bar to be 2px wide
            'elements' => [
                'bar' => [
                    'borderWidth' => 2,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                    'position' => 'right',
                ],
                'title' => [
                    'display' => false,
                    'text' => 'Chart.js Horizontal Bar Chart'
                ]
            ],
        ],
    ]); ?>
    <?php endif; ?>
    <?php // DEBUG VD::dump($personenKonten, 10, true); ?>

    <h3><?= Yii::t('app', 'Participants') ?></h3><!-- {{{ -->
    <div class="card-deck">
        <?php $persons = array_keys($personenKonten); sort($persons); ?>
        <?php foreach($persons as $person) : ?>
        <div class="card border-primary mb-3" style="max-width: 18rem;">
            <div class="card-body">
                <h5 class="card-title text-primary"><?= $person ?></h5>
                <p class="card-text"><?= Yii::t('app', '{person} has payed {countExpenses} expenses with the total value of {sumExpenses} ({sumExpensesSelf} for himself, and {sumExpensesOthers} for others).', [
                    'person'=>$person, 
                    'countExpenses'=>$participantDetails[$person]['countExpenses'], 
                    'sumExpenses'=>Yii::$app->formatter->asCurrency($participantDetails[$person]['sumExpenses'], $model->currency),
                    'sumExpensesSelf'=>Yii::$app->formatter->asCurrency($participantDetails[$person]['sumExpensesSelf'], $model->currency),
                    'sumExpensesOthers'=>Yii::$app->formatter->asCurrency($participantDetails[$person]['sumExpensesOthers'], $model->currency),
                ]) /* TRANSLATE */ ?>
                <?= Yii::t('app', 'Other participants have payed {countExpensesByOthers}x for {person}.', [
                    'person'=>$person, 
                    'countExpensesByOthers'=>$participantDetails[$person]['countExpensesByOthers'], 
                    'sumExpensesByOthers'=>Yii::$app->formatter->asCurrency($participantDetails[$person]['sumExpensesByOthers'], $model->currency)
                ]) ?>
                </p>
                <p class="card-text text-center">
                    <?php if($personenKonten[$person]<0) : ?>
                        <?= Yii::t('app', '{person} has, after billing of all payments and money transfers, debts with th eamount of {saldo}.', [
                            'person' => $person,
                            'saldo' => Yii::$app->formatter->asCurrency(abs($saldo), $model->currency)
                        ]) ?>
                    <?php else : ?>
                        <?= Yii::t('app', '{person} has, after billing of all payments and money transfers, currently no debts.', [
                            'person' => $person,
                        ]) ?>
                    <?php endif; ?>
                </p>
                <p class="card-text text-center"><b><?= Yii::t('app', 'Total value of the project for {person}:<br>{totalProjectValue}', [
                    'person'=>$person, 
                    'totalProjectValue' => Yii::$app->formatter->asCurrency($participantDetails[$person]['totalProjectValue'], $model->currency),
                ]) ?></b></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div><!-- }}} -->

    <h3><?= Yii::t('app', 'Compensation Payments') ?></h3>
    <?php // VD::dump($schlusszahlungen, 10, true); ?>
    <div class="card-deck">

        <div class="card border-primary mb-3" style="max-width: 18rem;">
            <div class="card-body">
                <h5 class="card-title text-primary"><?= Yii::t('app', 'All Final Compensations') ?></h5>
                <p class="card-text">
                    <ul>
                        <?php foreach($schlusszahlungen as $schlusszahlung) : ?>
                        <li><?= $schlusszahlung ?></li>
                        <?php endforeach; ?>
                    </ul>
                </p>
            </div>
        </div>

        <?php 
        /**
         * Simplified Final Money Transfers
         */
        $schlusszahlungen2  = [];
        $empfaenger         = '';
        foreach($personenKonten as $person=>$saldo) {
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
        ?>
        <div class="card border-primary mb-3" style="max-width: 18rem;">
            <div class="card-body">
                <h5 class="card-title text-primary"><?= Yii::t('app', 'Final Compensations (simplified)') ?></h5>
                <p class="card-text">
                    <?php if(count($schlusszahlungen2)===0) : ?>
                    <?= Yii::t('app', 'You are balanced!') ?><br>
                    <?= Yii::t('app', 'Nobody owes somebody some money.') ?>
                    <?php else : ?>
                    <ul>
                        <?php foreach($schlusszahlungen2 as $schlusszahlung) : ?>
                        <li>
                            <?= str_replace('__recipient__', $empfaenger, $schlusszahlung['text']) ?>
                            <?= Html::a(Html::icon('refresh-cw') . Yii::t('app', 'Compensate'), [
                                'expense/create', 
                                'Expense[costprojectId]'    =>$model->id, 
                                'Expense[title]'            =>'Ausgleichszahlung',
                                'Expense[expenseType]'      => \app\dictionaries\ExpenseTypesDict::EXPENSETYPE_TRANSFER,
                                'Expense[amount]'           =>$schlusszahlung['amount'], 
                                'Expense[payedBy]'          =>$schlusszahlung['person'], 
                                'Expense[splitting]'        =>'SELECTED',
                                'Expense[participants]'     =>$empfaenger
                            ], ['title' => Yii::t('app', 'Prepare this as a new expense'), 'class'=>'btn btn-primary btn-sm d-print-none']) ?>
                            <?= Html::tag('span', Yii::t('app', 'Open payment'), ['class'=>'badge badge-info d-none d-print-inline']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?> 
                </p>
            </div>
        </div>

    </div><!-- card-deck -->

    <?php // DEACTIVATED: endif; // count bilanzen > 0 ?>

</div>
