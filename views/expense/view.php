<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\widgets\DetailView;

use app\components\Html;
use app\models\Costitem;
use app\models\Expense;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Expense $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['costproject/index']];
$this->params['breadcrumbs'][] = ['label' => $model->costproject->recordName, 'url' => ['costproject/view', 'id' => $model->costprojectId]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expenses')];
$this->params['breadcrumbs'][] = $model->title;
\yii\web\YiiAsset::register($this);

// Get costitems for grid
$costitemsDataProvider = new ArrayDataProvider([
    'allModels' => $model->costitems,
    'key' => 'id',
    'pagination' => false,
]);
$splittingOptions = \app\models\Expense::getSplittingOptions();
?>
<div class="expense-view">

    <h1><?= Html::encode(Yii::t('app', 'Expense: {title}', ['title'=>$model->title])) ?></h1>

    <div class="btn-group mb-4" role="group" aria-label="Buttons">
        <?= Html::a(Html::icon('edit') . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Html::icon('trash-2') . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <?php if(!Yii::$app->mobileSwitcher->showMobile) : ?>

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-striped table-responsive-sm table-hover'],
        'attributes' => [
            /* [
                'attribute'=>'title',
                'format'=>'html',
                'value'=>Html::tag('h4', $model->title),
            ], */
            [
                'attribute'=>'costprojectId',
                'format'=>'html',
                'value'=>Html::a($model->costproject->title, ['costproject/view', 'id'=>$model->costprojectId])
                    . ' | '
                    . Html::a(Yii::t('app', 'Cost Breakdown'), ['costproject/breakdown', 'id'=>$model->costprojectId])
                    . ' | '
                    . Html::a(Yii::t('app', 'Add Expense'), ['/expense/create', 'Expense[costprojectId]'=>$model->costprojectId]),
            ],
            'payedBy',
            [
                'attribute'=>'itemDate',
                'value'=>Yii::$app->formatter->asDate($model->itemDate, 'medium'),
            ],
            [
                'attribute'=>'amount',
                'value' => Yii::$app->formatter->asCurrency($model->amount, $model->currency),
            ],
            [
                'attribute'=>'amount',
                'value' => sprintf('%s (%0.5f %s/%s)',
                    Yii::$app->formatter->asCurrency($model->amount * $model->exchangeRate, $model->costproject->currency),
                    $model->exchangeRate,
                    $model->costproject->currency,
                    $model->currency
                ),
                'visible' => $model->currency !== $model->costproject->currency,
            ],
            [
                'attribute'=>'splitting',
                'value'=>$splittingOptions[$model->splitting],
            ],
            // 'id',
        ],
    ]) ?>

    <?php else : ?>

    <div class="btn-group mb-4">
        <?= Html::a(Yii::t('app', 'Cost Breakdown'), ['costproject/breakdown', 'id'=>$model->costprojectId], ['class' => 'btn btn-primary btn-sm']) ?>
        <?= Html::a(Yii::t('app', 'Add Expense'), ['/expense/create', 'Expense[costprojectId]'=>$model->costprojectId], ['class' => 'btn btn-primary btn-sm']) ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['tag' => 'div', 'class' => 'list-group'],
        'template' => '<a class="list-group-item list-group-item-action"{contentOptions}><div class="d-flex w-100 justify-content-between"><h5>{label}</h4></div><p>{value}</p></a>',
        'attributes' => [
            /* [
                'attribute'=>'title',
                'format'=>'html',
                'value'=>Html::tag('h4', $model->title),
            ], */
            [
                'attribute'=>'costprojectId',
                'format'=>'html',
                'value'=>$model->costproject->title,
                'contentOptions' => ['href'=>Url::to(['costproject/view', 'id'=>$model->costprojectId])],
            ],
            'payedBy',
            [
                'attribute'=>'itemDate',
                'value'=>Yii::$app->formatter->asDate($model->itemDate, 'medium'),
            ],
            [
                'attribute'=>'amount',
                'value' => Yii::$app->formatter->asCurrency($model->amount, $model->currency),
            ],
            [
                'attribute'=>'amount',
                'value' => sprintf('%s (%0.5f %s/%s)',
                    Yii::$app->formatter->asCurrency($model->amount * $model->exchangeRate, $model->costproject->currency),
                    $model->exchangeRate,
                    $model->costproject->currency,
                    $model->currency
                ),
                'visible' => $model->currency !== $model->costproject->currency,
            ],
            [
                'attribute'=>'splitting',
                'value'=>$splittingOptions[$model->splitting],
            ],
            // 'id',
        ],
    ]) ?>
    
    <?php endif; ?>
    
    <h3><?= Yii::t('app', 'Cost Splitting') ?></h3>
    <?= GridView::widget([
        'id' => 'expenses-grid',
        'dataProvider' => $costitemsDataProvider,
        'tableOptions' => ['class' => 'table table-striped table-responsive-sm table-hover'],
        'summary' => Yii::t('app', 'Total <b>{count, number}</b> {count, plural, one{participant} other{participants}}.'),
        'columns' => [
            [
                'attribute'=>'participant',
            ],
            [
                'attribute' => 'weight',
                'label' => Yii::t('app', 'Distribution'),
                'contentOptions' => [ 'class' => 'text-center' ],
                'visible' => $model->splitting===Expense::SPLITTING_SELECTED_PARTICIPANTS_CUSTOM
            ],
            [
                'attribute'=>'amount',
                'contentOptions' => [ 'class' => 'text-right' ],
                'value' => function($data) {
                    return Yii::$app->formatter->asCurrency($data['amount'], $data->currency);
                },
            ],
            [
                'attribute' => 'exchangeRate',
                'contentOptions' => [ 'class' => 'text-right' ],
                'value' => function($data) use($model) {
                    return sprintf('%0.6f %s/%s', $data->exchangeRate, $data->currency, $model->costproject->currency);
                },
                'visible' => $model->costproject->useCurrency,
            ],
            [
                'label' => Yii::t('app', 'Amount {currency}', ['currency'=>$model->costproject->currency]),
                'contentOptions' => [ 'class' => 'text-right' ],
                'value' => function($data) use($model) {
                    return Yii::$app->formatter->asCurrency($data->amount * $data->exchangeRate, $model->costproject->currency);
                },
                'visible' => $model->costproject->useCurrency,
            ],
        ],
    ]) ?>

    <h3 class="mt-3"><?= Yii::t('app', 'Attachments') ?></h3>
    <?= count($model->documents)===0 ? Yii::t('app', '(no file attachments yet)') : '' ?>
    <?= \floor12\files\components\FileListWidget::widget([
        'files' => $model->documents, 
        'downloadAll' => true, 
        'zipTitle' => "Attachments of {$model->title}" 
    ]) ?>

    <h3><?= Yii::t('app', 'History') ?></h3><!-- {{{ -->
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
