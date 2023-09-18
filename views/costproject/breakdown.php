<?php

use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\helpers\VarDumper as VD;
use yii\widgets\DetailView;

use app\components\Html;
use app\dictionaries\CurrencyCodesDict;
use app\models\Expense;
use app\widgets\GridView;

/** @var yii\web\View $this */
/** @var app\models\Costproject $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\helpers\Url::remember('', 'cost-project');
\yii\web\YiiAsset::register($this);

// Get expenses for grid
$expensesDataProvider = new ArrayDataProvider([
    'allModels' => $model->expenses,
    'key' => 'id',
    'pagination' => [
        'pageSize' => 10,
    ],
]);
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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'title',
            [
                'attribute' => 'participants',
                'format' => 'html',
                'value' => nl2br($model->participants),
            ],
            [
                'attribute' => 'useCurrency',
                'format' => 'boolean',
            ],
            [
                'attribute' => 'currency',
                'value' => CurrencyCodesDict::get($model->currency),
                'visible' => $model->useCurrency,
            ],
        ],
    ]) ?>

    <h3><?= Yii::t('app', 'Expenses') ?></h3>
    <?php $breakdown = $model->getBreakdown(); ?>

    <?php $totalProjectCost = 0; $participantDetails = []; $participantSums = []; $sum = 0; $participants = array_values($model->participantsList); ?>
    <table class="table table-striped table-responsive table-hover" style="width:100%">
        <thead>
            <tr>
                <th><?= Yii::t('app', 'Date') ?></th>
                <th><?= Yii::t('app', 'Title') ?></th>
                <th><?= Yii::t('app', 'Amount') ?></th>
                <th><?= Yii::t('app', 'By') ?></th>
                <th><?= Yii::t('app', 'Participants') ?></th>
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
                <td class="text-center"><?= Yii::$app->formatter->asDate($row->itemDate, 'php:'.Yii::t('app', 'Y-m-d')) ?></td>
                <td><?= $row->title ?> <?= Html::a('.', ['/expense/update', 'id'=>$row->id], ['class'=>'d-print-none']) ?></td>
                <td class="text-right"><?= Yii::$app->formatter->asCurrency($row->amount, $row->currency) ?></td>
                <td class="text-center"><?= $row->payedBy ?></td>
                <td class="text-center"><?= $row->splitting==Expense::SPLITTING_EQUAL ? join(', ', $model->participantsList) : $row->participants ?></td>
                <?php if($model->useCurrency) : ?>
                <td class="text-right"><?= Yii::$app->formatter->asCurrency($row->amount * $row->exchangeRate, $model->currency) ?></td>
                <?php endif; ?>
                <!-- <td class="text-center"><?= Yii::$app->formatter->asDate($row->created_at, 'php:'.Yii::t('app', 'Y-m-d')) ?></td> -->
                <?php foreach($participants as $participant) : ?>
                <?php if(!array_key_exists($participant, $participantDetails)) $participantDetails[$participant] = $defaultParticipantDetails; ?>
                <td style="color:lightgreen" class="text-right">
                    <?php if($participant==$row->payedBy) : ?>
                    <?= Yii::$app->formatter->asDecimal($row->amount  * $row->exchangeRate, 2) ?>
                    <?php $participantSums[$participant] += $row->amount  * $row->exchangeRate; ?>
                    <?php if($row->title!=='Ausgleichszahlung') : ?>
                        <?php $participantDetails[$participant]['sumExpenses'] += $row->amount  * $row->exchangeRate; $participantDetails[$participant]['countExpenses']++; ?>
                    <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td style="color: red" class="text-right">
                    <?php foreach($row->costitems as $costitem) : ?>
                    <?php // if($costitem->amount==$row->amount) continue; ?>
                    <?php if($costitem->participant==$participant) : ?>
                        <?= Yii::$app->formatter->asDecimal($costitem->amount * $costitem->exchangeRate, 2) ?>
                        <?php $participantSums[$participant] -= $costitem->amount * $costitem->exchangeRate; ?>
                        <?php if($row->title!=='Ausgleichszahlung') : ?>
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
                        <?php if($row->payedBy===$participant and $row->title!=='Ausgleichszahlung') : ?>
                            <?php $participantDetails[$row->payedBy]['sumExpensesOthers'] += $costitem->amount * $costitem->exchangeRate; ?>
                        <?php endif; ?>
                    <?php endif; ?>
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

    <div class="card border-primary mb-3" style="max-width: 18rem;page-break-after: always;">
        <div class="card-header font-weight-bold"><?= Yii::t('app', 'Gesamtkosten des Projekts') ?></div>
        <div class="card-body text-primary">
            <h5 class="card-title text-center"><?= Yii::$app->formatter->asCurrency($model->totalExpenses, $model->currency) ?></h5>
        </div>
    </div>
<?php
$headers = [];
$bilanzen = [];
$schlusszahlungen = [];
$personenKonten = [];
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
<?php if(count($bilanzen)==0) : ?>
    <?= '(no expenses found)' ?>
<?php else : ?>

<?php
foreach($bilanzen as $bilanzKey=>$partnerStaende) {
    $partners = explode('|', $bilanzKey);
    $saldo = $partnerStaende[$partners[0]] - $partnerStaende[$partners[1]];
    // echo $bilanzKey.': '.$saldo.'<br>';
    if($saldo>0) {
        $schlusszahlungen[$bilanzKey] = $partners[1] . ' schuldet '.$partners[0] . ' ' . Yii::$app->formatter->asCurrency($saldo, $model->currency);
        $personenKonten[$partners[0]] += $saldo;
        $personenKonten[$partners[1]] -= $saldo;
    } elseif($saldo<0) {
        $schlusszahlungen[$bilanzKey] = $partners[0] . ' schuldet '.$partners[1] . ' ' . Yii::$app->formatter->asCurrency(abs($saldo), $model->currency);
        $personenKonten[$partners[0]] -= abs($saldo);
        $personenKonten[$partners[1]] += abs($saldo);
    }
}
sort($schlusszahlungen);
ksort($personenKonten);
$amounts = array_values($personenKonten);
?>
    <h3><?= 'Bilanz' ?></h3>
    <table class="table table-striped">
        <thead>
        </thead>
        <tbody>
            <?php foreach($personenKonten as $person => $saldo) : ?>
            <tr>
                <td class="<?= '' // $saldo<0 ? 'progress' :'' ?>"><?php if($saldo<0) : ?><div class="progress-bar float-right" role="progressbar" style="width: <?= abs($saldo)/max($amounts)*100 ?>%;" aria-valuenow="<?= abs($saldo)/max($amounts)*100 ?>" aria-valuemin="0" aria-valuemax="100"><?= Yii::$app->formatter->asCurrency($saldo, $model->currency) ?></div><?php endif; ?></td>
                <td class="text-center"><h5><span class="badge badge-primary"><?= $person ?></span></h5></td>
                <td class="<?= '' // $saldo>0 ? 'progress' : '' ?>"><?php if($saldo>0) : ?><div class="progress-bar float-left" role="progressbar" style="width: <?= $saldo/max($amounts)*100 ?>%;" aria-valuenow="<?= $saldo/max($amounts)*100 ?>" aria-valuemin="0" aria-valuemax="100"><?= Yii::$app->formatter->asCurrency($saldo, $model->currency) ?></div><?php endif; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php
// DEBUG VD::dump($personenKonten, 10, true);
?>

    <h3><?= Yii::t('app', 'Participants') ?></h3>
    <div class="card-deck">
        <?php $persons = array_keys($personenKonten); sort($persons); ?>
        <?php foreach($persons as $person) : ?>
        <div class="card border-primary mb-3" style="max-width: 18rem;">
            <div class="card-body">
                <h5 class="card-title text-primary"><?= $person ?></h5>
                <p class="card-text"><?= Yii::t('app', '{person} hat {countExpenses} Ausgabe(n) im Gesamtwert von {sumExpenses} getätigt ({sumExpensesSelf} für sich selbst und {sumExpensesOthers} für andere).', [
                    'person'=>$person, 
                    'countExpenses'=>$participantDetails[$person]['countExpenses'], 
                    'sumExpenses'=>Yii::$app->formatter->asCurrency($participantDetails[$person]['sumExpenses'], $model->currency),
                    'sumExpensesSelf'=>Yii::$app->formatter->asCurrency($participantDetails[$person]['sumExpensesSelf'], $model->currency),
                    'sumExpensesOthers'=>Yii::$app->formatter->asCurrency($participantDetails[$person]['sumExpensesOthers'], $model->currency),
                ]) ?>
                <?= Yii::t('app', 'Andere Teilnehmer haben {countExpensesByOthers}x etwas für {person} bezahlt ({sumExpensesByOthers}).', [
                    'person'=>$person, 
                    'countExpensesByOthers'=>$participantDetails[$person]['countExpensesByOthers'], 
                    'sumExpensesByOthers'=>Yii::$app->formatter->asCurrency($participantDetails[$person]['sumExpensesByOthers'], $model->currency)
                ]) ?>
                </p>
                <p class="card-text text-center">
                    <?php if($personenKonten[$person]<0) : ?>
                        <?= Yii::t('app', '{person} hat nach Verrechnung aller Zahlungen und Geldübergaben noch Schulden in Höhe von {saldo}.', [
                            'person' => $person,
                            'saldo' => Yii::$app->formatter->asCurrency($saldo, $model->currency)
                        ]) ?>
                    <?php else : ?>
                        <?= Yii::t('app', '{person} hat nach Verrechnung aller Zahlungen und Geldübergaben aktuell keine Schulden.', [
                            'person' => $person,
                        ]) ?>
                    <?php endif; ?>
                </p>
                <p class="card-text text-center"><b><?= Yii::t('app', 'Gesamtwert des Projekts für {person}:<br>{totalProjectValue}', [
                    'person'=>$person, 
                    'totalProjectValue' => Yii::$app->formatter->asCurrency($participantDetails[$person]['totalProjectValue'], $model->currency),
                ]) ?></b></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <h3><?= Yii::t('app', 'Compensation Payments') ?></h3>
<?php
// echo Html::tag('h3', 'Schlusszahlungen');
// VD::dump($schlusszahlungen, 10, true);
?>
    <div class="card-deck">

        <div class="card border-primary mb-3" style="max-width: 18rem;">
            <div class="card-body">
                <h5 class="card-title text-primary"><?= 'Alle Schlusszahlungen' ?></h5>
                <p class="card-text">
                    <ul>
                        <?php foreach($schlusszahlungen as $schlusszahlung) : ?>
                        <li><?= $schlusszahlung ?></li>
                        <?php endforeach; ?>
                    </ul>
                </p>
            </div>
        </div>

<?php $schlusszahlungen2 = [];
$empfaenger = '';
foreach($personenKonten as $person=>$saldo) {
    if($saldo>0)
        $empfaenger = $person;
    elseif($saldo<0)
        $schlusszahlungen2[] = ['amount'=>abs($saldo), 'person'=>$person, 'text'=>$person . ' schuldet __empfaenger__ ' . Yii::$app->formatter->asCurrency(abs($saldo), $model->currency)];
}
?>
        <div class="card border-primary mb-3" style="max-width: 18rem;">
            <div class="card-body">
                <h5 class="card-title text-primary"><?= 'Schlusszahlungen (vereinfacht)' ?></h5>
                <p class="card-text">
                    <?php if(count($schlusszahlungen2)===0) : ?>
                    <?= 'Ihr seid ausgeglichen!' ?><br>
                    <?= 'Keiner schuldet jemandem Geld.' ?>
                    <?php else : ?>
                    <ul>
                        <?php foreach($schlusszahlungen2 as $schlusszahlung) : ?>
                        <li>
                            <?= str_replace('__empfaenger__', $empfaenger, $schlusszahlung['text']) ?>
                            <?= Html::a(Html::icon('refresh-cw') . Yii::t('app', 'Compensate'), [
                                'expense/create', 
                                'Expense[costprojectId]'=>$model->id, 
                                'Expense[title]'=>'Ausgleichszahlung',
                                'Expense[amount]'=>$schlusszahlung['amount'], 
                                'Expense[payedBy]'=>$schlusszahlung['person'], 
                                'Expense[splitting]'=>'SELECTED',
                                'Expense[participants]'=>$empfaenger
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
    <?php endif; ?>

</div>

