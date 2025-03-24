<?php

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

$this->title = Yii::t('app', '{title} / Cost Breakdown', ['title' => $model->title]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Show participants column?
$showParticipants = false;

$defaultParticipantDetails = [
    'sumExpenses'=>0, 
    'countExpenses'=>0, 
    'sumExpensesSelf'=>0, 
    'sumExpensesOthers'=>0, 
    'totalProjectValue'=>0, 
    'countExpensesByOthers'=>0, 
    'sumExpensesByOthers'=>0
];

// Convert float to currency
function float2currency($array, $currency) {
    foreach($array as $key=>$value)
        $array[$key] = Yii::$app->formatter->asCurrency($value, $currency);
    return $array;
}

// Print an array as HTML table
function array2table($array,$headers = null)
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
<?php foreach ($array as $row): array_map('htmlentities', $row); ?>
    <tr>
      <td><?php echo implode('</td><td>', $row); ?></td>
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

<?php $breakdown = $model->getBreakdown(); ?>

<?php $totalProjectCost = 0; $participantDetails = []; $participantSums = []; $sum = 0; ?>

<h2><?= Yii::t('app', 'Total Expenses') ?></h2>
<?= Yii::t('app', 'The total cost of the project is <b>{totalExpenses}</b>.', ['totalExpenses' => Yii::$app->formatter->asCurrency($totalExpenses, $model->currency)]) ?>

<h2><?= Yii::t('app', 'Expenses') ?></h2>
<?= array2table($display) ?>

<h2><?= Yii::t('app', 'Participant Replacements') ?></h2>
<?= array2table(array(array_values($replaceNames)), array_keys($replaceNames)); ?>

<h2><?= Yii::t('app', 'Participants') ?></h2>
<?= '<ul><li>'.join('</li><li>', $participants).'</li></ul>'; ?>

<h2><?= Yii::t('app', 'Expenses') ?></h2>
<?= array2table(array(0=>float2currency($participantExpenses, $model->currency))); ?>

<h2><?= Yii::t('app', 'Participation') ?></h2>
<?= array2table(array(float2currency($participantParticipation, $model->currency))); ?>

<h2><?= Yii::t('app', 'Balance') ?></h2>
<?= array2table(array(float2currency($participantBalance, $model->currency))); ?>
<p><?= Yii::t('app', 'A negative amount means: Amount to be received.') ?></p>

<h2><?= Yii::t('app', 'Compensation Payments') ?></h2>
<?= array2table($merged); ?>
<p><?= Yii::t('app', 'A negative amount means: Amount to be received.') ?></p>

<h2><?= Yii::t('app', 'Settlement Payments') ?></h2>
<?php for($i=1; $i<count($compensation); $i++) : ?>
<b><?= Yii::t(
    'app', 
    '{debitor} pays to {recipient} {amount}', 
    [
        'debitor' => $participants[$compensation[$i][Yii::t('app', 'Debitor')]-1], 
        'recipient' => $participants[$compensation[$i][Yii::t('app', 'Recipient')]-1], 
        'amount' => Yii::$app->formatter->asCurrency($compensation[$i][Yii::t('app', 'Amount')], $model->currency)
    ]
) ?></b><br>    
<?php endfor; ?>

</div>