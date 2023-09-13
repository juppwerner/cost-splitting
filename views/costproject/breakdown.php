<?php

use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

use app\models\Expense;
use app\widgets\GridView;

/** @var yii\web\View $this */
/** @var app\models\Costproject $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// Get expenses for grid
$expensesDataProvider = new ArrayDataProvider([
    'allModels' => $model->expenses,
    'key' => 'id',
    'pagination' => [
        'pageSize' => 10,
    ],
]);
?>
<div class="costproject-breakdown">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-primary d-print-none']) ?>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary d-print-none']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title',
            [
                'attribute' => 'participants',
                'value' => str_replace("\n", ', ', $model->participants),
            ],
            'currency',
        ],
    ]) ?>

    <h3><?= Yii::t('app', 'Cost Breakdown') ?></h3>
    <?php $breakdown = $model->getBreakdown(); ?>

    <?php $participantSums = []; $sum = 0; $participants = array_values($model->participantsList); ?>
    <table class="table table-striped table-responsive table-hover" style="width:100%">
        <thead>
            <tr>
                <th><?= Yii::t('app', 'Date') ?></th>
                <th><?= Yii::t('app', 'Title') ?></th>
                <th><?= Yii::t('app', 'By') ?></th>
                <th><?= Yii::t('app', 'Participants') ?></th>
                <th><?= Yii::t('app', 'Amount') ?></th>
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
            <tr>
                <td class="text-center"><?= Yii::$app->formatter->asDate($row->itemDate, 'php:'.Yii::t('app', 'Y-m-d')) ?></td>
                <td><?= $row->title ?> <?= Html::a('.', ['/expense/update', 'id'=>$row->id], ['class'=>'d-print-none']) ?></td>
                <td class="text-center"><?= $row->payedBy ?></td>
                <td class="text-center"><?= $row->splitting==Expense::SPLITTING_EQUAL ? join(', ', $model->participantsList) : $row->participants ?></td>
                <td class="text-right"><?= Yii::$app->formatter->asCurrency($row->amount, $row->currency) ?></td>
                <?php if($model->useCurrency) : ?>
                <td class="text-right"><?= Yii::$app->formatter->asCurrency($row->amount * $row->exchangeRate, $model->currency) ?></td>
                <?php endif; ?>
                <!-- <td class="text-center"><?= Yii::$app->formatter->asDate($row->created_at, 'php:'.Yii::t('app', 'Y-m-d')) ?></td> -->
                <?php foreach($participants as $participant) : ?>
                <td style="color:lightgreen" class="text-right">
                    <?php if($participant==$row->payedBy) : ?>
                    <?= Yii::$app->formatter->asDecimal($row->amount  * $row->exchangeRate, 2) ?>
                    <?php $participantSums[$participant] += $row->amount  * $row->exchangeRate; endif; ?>
                </td>
                <td style="color: red" class="text-right">
                    <?php foreach($row->costitems as $costitem) : ?>
                    <?php if($costitem->amount==$row->amount) continue; ?>
                    <?php if($costitem->participant==$participant) : ?>
                    <?= Yii::$app->formatter->asDecimal($costitem->amount * $costitem->exchangeRate, 2) ?>
                    <?php $participantSums[$participant] += $costitem->amount * $costitem->exchangeRate; endif; ?>
                    <?php endforeach; ?>
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="<?= 5 + (int)$model->useCurrency ?>">&nbsp;</td>
                <?php foreach($participants as $participant) : ?>
                <?php $sum += $participantSums[$participant]; ?>
                <td colspan="2" class="text-right"><?= Yii::$app->formatter->asCurrency($participantSums[$participant], 'EUR') ?></td>
                <?php endforeach; ?>
        </tbody>
    </table>

<?php
// Functions to simulate Excel:
function zeilensumme($participantExpenses, &$matrix, $p) // {{{ 
{
    $participants = array_keys($participantExpenses);
    
    $pKeys = array_flip($participants);
    $sum = $participantExpenses[$p];
    foreach($participants as $iSp=>$pSp) {
        if($pKeys[$p]>=$iSp)
            break;
        $sum += $matrix[$p][$pSp];
    }
    return $sum;
} // }}} 
function spaltensumme($participants, &$matrix, $p, $zeile) // {{{ 
{

    $sum = 0;
    foreach($participants as $iZ=>$pZ) {
        if($iZ>=$zeile)
            break;
        $sum += $matrix[$pZ][$p];
    }    
    return $sum;
} // }}} 

/* in E5:
    =($A5<>E$2)*(                                                   // Name Z <> Name Sp?
        ($B5<=$B$1)*(                                               // $expense[$pR]<=$average
            (SUMME($B5:D5)<>$B$1)                                   // f1 Zeilensumme bis links vorher
            * (INDEX($B:$B;SPALTE())>=$B$1)                         // f2
            * (
                (SUMME(E$2:E4; INDEX($B:$B;SPALTE()))>$B$1)         // f3 Spaltensumme + expense > average
                * (MIN(
                    $B$1-SUMME($B5:D5);                             // f4 $average - zeilensumme
                    -SUMME(E$2:E4) + INDEX($B:$B;SPALTE()) - $B$1)  // f5 spaltensumme + $expense - $average
                )
            )
        )
    )
 */
$participantExpenses = [];
$participantDiffToAvg = [];
foreach($participants as $participant) 
    $participantExpenses[$participant] = 0;

foreach($breakdown as $expense) {
    $participantExpenses[$expense->payedBy] += $expense->amount * $expense->exchangeRate;
}

// Calculate average
$average = array_sum(array_values($participantExpenses))/count(array_values($participantExpenses));

// Diff to average
foreach($participants as $participant) 
    $participantDiffToAvg[$participant] = $participantExpenses[$participant] - $average;

$matrix = [];
foreach($participants as $iR=>$pR) {
    $matrix[$pR]= [];
    foreach($participants as $iSp=>$pSp) {
        $matrix[$pR][$pSp] = null;
        // Zeile <> Spalte?
        if($pR==$pSp) {
            $matrix[$pR][$pSp] = 0;
            continue;
        }
        if($participantExpenses[$pR]>$average) {
            $matrix[$pR][$pSp] = 0;
            continue;
        }
        $f1 = zeilensumme($participantExpenses, $matrix, $pR)<>$average;
        $f2 = $participantExpenses[$pSp]>=$average;
        $f3 = (spaltensumme($participants, $matrix, $pSp, $iR)+$participantExpenses[$pSp])>$average;
        $f4 = $average - zeilensumme($participantExpenses, $matrix, $pR);
        $f5 = -1*spaltensumme($participants, $matrix, $pSp, $iR) + $participantExpenses[$pSp] - $average;
        $cell = ($participantExpenses[$pR]<=$average)*(
            $f1
            * $f2
            * (
                $f3
                * min(
                    $f4,
                    $f5
                )
            )
        );
        $matrix[$pR][$pSp] = sqrt($cell*$cell);
    }
}
// DEBUG \yii\helpers\VarDumper::dump($matrix, 10, true);
?>
    <h3><?= Yii::t('app', 'Compensation Payments') ?></h3>
    <table class="table table-striped table-responsive table-hover table-sm">
        <tbody>
        <?php foreach($matrix as $pR=>$row) : ?>
        <?php foreach($row as $pSp=>$amount) : if($amount==0) continue; ?>
            <tr>
                <td><?= Yii::t('app', '{participant} owes {creditor}:', ['participant'=>$pR, 'creditor'=>$pSp]) ?></td>
                <td class="text-right"> <?= Yii::$app->formatter->asCurrency($amount, $model->currency) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
