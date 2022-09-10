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
    'sort' => [
        'attributes' => [
            'itemDate',
            'title',
            'amount',
            'payedBy',
        ],
        'defaultOrder' => [
            'itemDate' => SORT_DESC,
            'title' => SORT_ASC,
        ],
    ],
]);
?>
<div class="costproject-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Cost Breakdown'), ['breakdown', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title',
            [
                'attribute' => 'participants',
                'format' => 'html',
                'value' => nl2br($model->participants),
            ],
            'id',
        ],
    ]) ?>

    <h3><?= Yii::t('app', 'Expenses') ?></h3>
    <p><?= Html::a(Yii::t('app', 'Add Expense'), ['/expense/create', 'Expense[costprojectId]'=>$model->id], ['class' =>  'btn btn-primary btn-sm']) ?></p>
    <?= GridView::widget([
        'id' => 'expenses-grid',
        'dataProvider' => $expensesDataProvider,
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
            'title',
            [
                'attribute'=>'amount',
                'contentOptions' => [ 'class' => 'text-right' ],
                'value' => function($data) {
                    return Yii::$app->formatter->asCurrency($data['amount'], 'EUR');
                },
            ],
            [
                'attribute'=>'payedBy',
                'contentOptions' => ['class'=>'text-center'],
            ],
        ],
    ]) ?>
    <p>
        <?= Html::a(Yii::t('app', 'Add Expense'), ['/expense/create', 'Expense[costprojectId]'=>$model->id], ['class' =>  'btn btn-primary btn-sm']) ?>
        <?= Html::a(Yii::t('app', 'All Expenses'), ['/expense/index', 'ExpenseSearch[costprojectId]'=>$model->id], ['class' =>  'btn btn-primary btn-sm']) ?>
    </p>

    <h3><?= Yii::t('app', 'History') ?></h3><!-- {{{ -->
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute'=>'created_at', 'format'=>'html', 'value'=>Yii::$app->formatter->asDateTime($model->created_at)],
            ['attribute'=>'createUserName', 'format'=>'html'],
            ['attribute'=>'updated_at', 'format'=>'html', 'value'=>Yii::$app->formatter->asDateTime($model->updated_at)],
            ['attribute'=>'updateUserName', 'format'=>'html'],
        ],
        ])
    ?><!-- }}} -->
</div>
