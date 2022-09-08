<?php

use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\widgets\DetailView;

use app\models\Costitem;
use app\widgets\GridView;

/** @var yii\web\View $this */
/** @var app\models\Expense $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
\yii\web\YiiAsset::register($this);

// Get costitems for grid
$costitemsDataProvider = new ArrayDataProvider([
    'allModels' => $model->costitems,
    'key' => 'id',
    'pagination' => false,
]);
?>
<div class="expense-view">

    <h1><?= Html::encode(Yii::t('app', 'Expense: {title}', ['title'=>$model->title])) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            [
                'attribute'=>'costprojectId',
                'format'=>'html',
                'value'=>Html::a($model->costproject->title, ['costproject/view', 'id'=>$model->costprojectId])
                    . ' | '
                    . Html::a(Yii::t('app', 'Cost Breakdown'), ['costproject/breakdown', 'id'=>$model->costprojectId])
                    . ' | '
                    . Html::a(Yii::t('app', 'Add Expense'), ['/expense/create', 'Expense[costprojectId]'=>$model->costprojectId]),
            ],
            'title',
            'payedBy',
            [
                'attribute'=>'itemDate',
                'value'=>Yii::$app->formatter->asDate($model->itemDate, 'medium'),
            ],
            [
                'attribute'=>'amount',
                'value' => Yii::$app->formatter->asCurrency($model->amount, 'EUR'),
            ],
            'splitting',
            // 'id',
        ],
    ]) ?>

    <h3><?= Yii::t('app', 'Cost Splitting') ?></h3>
    <?= GridView::widget([
        'id' => 'expenses-grid',
        'dataProvider' => $costitemsDataProvider,
        'columns' => [
            [
                'attribute'=>'participant',
                'contentOptions' => ['class'=>'text-center'],
            ],
            [
                'attribute'=>'amount',
                'contentOptions' => [ 'class' => 'text-right' ],
                'value' => function($data) {
                    return Yii::$app->formatter->asCurrency($data['amount'], 'EUR');
                },
            ],
        ],
    ]) ?>

</div>
