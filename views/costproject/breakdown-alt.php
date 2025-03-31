<?php

use app\models\Costproject;
use yii\helpers\Url;
use yii\helpers\VarDumper as VD;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use yii\web\JsExpression;

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
/** @var mixed $participants Array with names of participants */

$this->title = Yii::t('app', '{title} / Cost Breakdown', ['title' => $model->title]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Show participants column?
$showParticipants = false;

// $defaultParticipantDetails = [
//     'sumExpenses'=>0, 
//     'countExpenses'=>0, 
//     'sumExpensesSelf'=>0, 
//     'sumExpensesOthers'=>0, 
//     'totalProjectValue'=>0, 
//     'countExpensesByOthers'=>0, 
//     'sumExpensesByOthers'=>0
// ];

// Convert float to currency
function float2currency($array, $currency) {
    foreach($array as $key=>$value)
        $array[$key] = Yii::$app->formatter->asCurrency($value, $currency);
    return $array;
}

// Print an array as HTML table
function array2table($array,$headers = null, $format = null, $currency=null)
{
    if(is_null($headers))
        $headers = array_keys(current($array));

    ob_start();
?>
<?php if (count($array) > 0): ?>
<table class="table table-striped">
  <thead>
    <tr>
      <th><?php echo implode('</th><th>',$headers); ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($array as $n=> $row): array_map('htmlentities', $row); ?>
    <tr>
      <?php $c = 0; foreach($row as $cell) : ?>
      <?php $class = '';
        if ($format == 'compensation-table' && $c >= 2) {
            if (abs((float)$cell) < 0.01) {
                $cell = '';
            } else {
                $class = 'text-right pr-4';
                if((float)$cell<0) {
                    $cell = abs($cell);
                    if($c>=3) $class .= ' text-primary';
                } else {
                    if($c>=3) $class .= ' text-danger';
                }
                $cell = Yii::$app->formatter->asCurrency($cell, $currency);
            }
        }
        if (trim($class) !== '')
            $class = ' class="' . $class . '"';
      ?>
      <td<?= $class ?>><?= $cell ?></td>
      <?php $c++; endforeach; ?>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
<?php    
    return ob_get_clean();
}
?>
<style>
div.costproject-breakdown-alt h2 { margin-top: 1em;}
</style>
<div class="costproject-breakdown-alt">

<h1><?= Html::encode($this->title) ?></h1>

<div class="btn-group mb-3" role="group" aria-label="Buttons">
    <?= Html::a(Html::icon('edit') . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm d-print-none']) ?>
    <?= Html::a(Html::icon('eye') . Yii::t('app', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-info btn-sm d-print-none']) ?>
    <?= Html::a(Html::icon('file-text') . Yii::t('app', 'PDF'), ['breakdown-pdf', 'id' => $model->id], ['target' => '_blank', 'class' => 'btn btn-info btn-sm d-print-none']) ?>
</div>

<!-- Cost Project Detail View -->
<?= $this->render('_view', ['model' => $model, 'showUserBtns' => false]) ?>
<?= $this->render('_currencyNotes') ?>

<?php // $breakdown = $model->getBreakdown(); ?>

<?php // $totalProjectCost = 0; $participantDetails = []; $participantSums = []; $sum = 0; ?>

<h2><?= Yii::t('app', 'Total Expenses') ?></h2>
<?= Yii::t('app', 'The total cost of the project is <b>{totalExpenses}</b>.', ['totalExpenses' => Yii::$app->formatter->asCurrency($totalExpenses, $model->currency)]) ?>

<h2><?= Yii::t('app', 'Expenses') ?></h2>
<?= '' // array2table($display) ?>
<?= '' /* GridView::widget([
    'dataProvider' => $expensesDataProvider,
    'columns' => [
        [
            'attribute' => 'date',
            'label' => Yii::t('app', 'Date'),
            'value' => function ($data) {
                return Yii::$app->formatter->asDate($data['date']);
            }
        ],
        [
            'attribute' => 'what',
            'label' => Yii::t('app', 'Expense'),
        ],
        [
            'attribute' => 'name',
            'label' => Yii::t('app', 'Paid By'),

        ],
        [
            'attribute' => 'expense',
            'label' => Yii::t('app', 'Expense'),
            'contentOptions' => ['class' => 'text-right pr-5'],
            'visible' => $model->useCurrency,
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency($data['expense'], $data['currency']);
            }
        ],
        [
            'attribute' => 'exchangeRate',
            'label' => Yii::t('app', 'Exchange Rate'),
            'contentOptions' => ['class' => 'text-right pr-5'],
            'visible' => $model->useCurrency,
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data['exchangeRate']);
            },
        ],
        [
            'attribute' => 'amount',
            'label' => Yii::t('app', 'Amount'),
            'contentOptions' => ['class' => 'text-right pr-5'],
            'value' => function ($data) use ($model) {
                return Yii::$app->formatter->asCurrency($data['amount'], $model->currency);
            }
        ],
        [
            'attribute' => 'method',
            'label' => Yii::t('app', 'Splitting'),
            'value' => function ($data) {
                if ($data['method'] == Expense::SPLITTING_EQUAL)
                    return Expense::getSplittingOptions()[$data['method']];
                if ($data['method'] == Expense::SPLITTING_SELECTED_PARTICIPANTS)
                    return $data['weights'];
            }
        ],
    ],
]) */
?>
<?= $this->render('_expenses-table', [
    'model' => $model,
    'breakdown' => $model->getBreakdown()
]) ?>

<h2><?= Yii::t('app', 'Participant Replacements') ?></h2>
<p><?= Yii::t('app', 'Shows if participants will be payed by other participants.') ?></p>
<?= array2table(array(array_values($replaceNames)), array_keys($replaceNames)); ?>

<h2><?= Yii::t('app', 'Expenses') ?></h2>
<?= array2table(array(0=>float2currency($participantExpenses, $model->currency))); ?>

<h2><?= Yii::t('app', 'Participation') ?></h2>
<?= array2table(array(float2currency($participantParticipation, $model->currency))); ?>

<!--
<h2><?= Yii::t('app', 'Balance') ?></h2>
<?= array2table(array(float2currency($participantBalance, $model->currency))); ?>
<p><?= Yii::t('app', 'A negative amount means: Amount to be received.') ?></p>
-->

<h2><?= Yii::t('app', 'Compensation Payments') ?></h2>
<?= array2table($merged, null, 'compensation-table', $model->currency); ?>

<h2><?= Yii::t('app', 'Settlement Payments') ?></h2>
<?php for($i=1; $i<count($compensation); $i++) : ?>
<p style="font-size: 1.2rem; line-height: 1" class="font-weight-bold"><?= Yii::t(
    'app', 
    '{debitor} owes {recipient} {amount}.', 
    [
        'debitor' => $participants[$compensation[$i][Yii::t('app', 'Debitor')]-1], 
        'recipient' => $participants[$compensation[$i][Yii::t('app', 'Recipient')]-1], 
        'amount' => Yii::$app->formatter->asCurrency($compensation[$i][Yii::t('app', 'Amount')], $model->currency)
    ]
) ?></p>   
<?php endfor; ?>

</div>