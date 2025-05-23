<?php

use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\web\YiiAsset;

use app\components\Html;
use app\dictionaries\Ewf;
use app\models\Expense;
// use app\widgets\GridView;


/** @var yii\web\View $this */
/** @var app\models\Costproject $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

Url::remember('', 'cost-project');
YiiAsset::register($this);

// Get expenses for grid
$expensesDataProvider = new ActiveDataProvider([
    //'allModels' => $model->expenses,
    //'key' => 'id',
    'query'=>$model->getExpenses(),
    'pagination' => [
        'pageSize' => 10,
    ],
    'sort' => [
        'attributes' => [
            'itemDate',
            'title',
            'amount',
            'payedBy',
        ],
        'defaultOrder' => [
            'itemDate' => SORT_DESC,
            'title' => SORT_DESC,
        ],
    ],
]);
?>
<div class="costproject-view">

    <h1><?= Yii::t('app', 'Cost Project: {title}', ['title' => Html::encode($this->title)]) ?></h1>

    <div class="btn-group mb-3" role="group" aria-label="Buttons">
        <?= Html::a(Html::icon('edit') . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
        <?= Html::a(Html::icon('plus-square') . Yii::t('app', 'Add Expense'), ['/expense/create', 'Expense[costprojectId]'=>$model->id], ['class' =>  'btn btn-primary btn-sm']) ?>
        <?= Html::a(Html::icon('file-text') . Yii::t('app', 'Cost Breakdown'), ['breakdown-alt', 'id' => $model->id], ['class' => 'btn btn-info btn-sm']) ?>
        <?= Html::a(Html::icon('download') . Yii::t('app', 'Export'), ['export', 'id' => $model->id], ['class' => 'btn btn-info btn-sm']) ?>
        <?= Html::a(Html::icon('trash-2') . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <!-- Cost Project Detail View -->
    <?= $this->render('_view', ['model' => $model]) ?>
    <?= $this->render('_currencyNotes') ?>

    <h3><?= Yii::t('app', 'Expenses') ?></h3>
    <p><?= Yii::t('app', 'This is the list of all expenses in this project.') ?></p>
    <?= GridView::widget([
        'id' => 'expenses-grid',
        'dataProvider' => $expensesDataProvider,
        'tableOptions' => ['class' => 'table table-striped table-responsive-sm table-hover'],
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                // you may configure additional properties here
                'template' => '{view}&nbsp;{update}',
                'contentOptions' => [ 'class' => 'text-center text-nowrap' ],
                'urlCreator' => function ($action, Expense $model, $key, $index, $column) {
                    return Url::toRoute(['/expense/'.$action, 'id' => $model->id]);
                 }
            ],
            [
                'attribute'=>'itemDate',
                'value'=>function($data) {
                    return Yii::$app->formatter->asDate($data['itemDate'], 'php:'.Yii::t('app', 'Y-m-d'));

                },
                'contentOptions' => ['class'=>'text-center'],
            ],
            [
                'attribute'=>'title',
                'format' => 'raw',
                'value' => function($data) {
                    $result = $data->title;
                    // if($data->expenseType===\app\dictionaries\ExpenseTypesDict::EXPENSETYPE_TRANSFER)
                    //     $result .= ' <span class="badge badge-info">'.Yii::t('app', 'Money Transfer').'</span>';
                    return Html::a(Html::tag('b', $result), ['expense/view', 'id'=>$data->id]);
                },
            ],
            [
                'label' => Yii::t('app', 'Documents'),
                'contentOptions' => [ 'class' => 'text-center' ],
                'value' => function($data) {
                    return count($data->documents);
                },
            ],
            [
                'attribute'=>'payedBy',
                // 'contentOptions' => ['class'=>'text-center'],
            ],
            [
                'label' => Yii::t('app', 'Participants'),
                'format' => 'html',
                'contentOptions' => ['style' => 'font-size: smaller'],
                'value' => function($data) {
                    if($data->splitting == Expense::SPLITTING_SELECTED_PARTICIPANTS_CUSTOM) {
                        $tmp = [];
                        $participantWeightings = \yii\helpers\Json::decode($data->splitting_weights);
                        foreach($participantWeightings as $participant => $weighting) {
                            $tmp[] = sprintf('%s - %sx', $participant, $weighting);
                        }
                        return join('<br>', $tmp);
                    }
                    return join('<br>', $data->getParticipants());
                },
            ],
            [
                'attribute'=>'amount',
                'contentOptions' => [ 'class' => 'text-right' ],
                'value' => function($data) {
                    return Yii::$app->formatter->asCurrency($data['amount'], $data['currency']);
                },
            ],
            [
                'attribute' => 'exchangeRate',
                'visible' => $model->useCurrency,
                'contentOptions' => [ 'class' => 'text-right' ],
                'value' => function($data) use($model) {
                    return sprintf('%0.6f %s/%s', $data['exchangeRate'], $data['currency'], $model->currency);
                },
             ],
             [
                'label' => Yii::t('app', 'Amount {currency}', ['currency'=>$model->currency]),
                'visible' => $model->useCurrency,
                'contentOptions' => [ 'class' => 'text-right' ],
                'value' => function($data) use($model) {
                    return Yii::$app->formatter->asCurrency($data->amount * $data->exchangeRate, $model->currency);
                },
             ],
        ],
    ]) ?>
    <p>
        <?= Html::a(Html::icon('plus-square') . Yii::t('app', 'Add Expense'), ['/expense/create', 'Expense[costprojectId]'=>$model->id], ['class' =>  'btn btn-primary btn-sm']) ?>
        <?= Html::a(Html::icon('list') . Yii::t('app', 'All Expenses'), ['/expense/index', 'ExpenseSearch[costprojectId]'=>$model->id], ['class' =>  'btn btn-primary btn-sm']) ?>
    </p>

    <h3 class="mt-5"><?= Yii::t('app', 'History') ?></h3><!-- {{{ -->
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-striped table-responsive-sm table-hover'],
        'attributes' => [
            ['attribute'=>'created_at', 'format'=>'html', 'value'=>Yii::$app->formatter->asDateTime($model->created_at)],
            ['attribute'=>'createUserName', 'format'=>'html'],
            ['attribute'=>'updated_at', 'format'=>'html', 'value'=>Yii::$app->formatter->asDateTime($model->updated_at)],
            ['attribute'=>'updateUserName', 'format'=>'html'],
        ],
        ])
    ?><!-- }}} -->
</div>
