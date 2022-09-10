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
$splittingOptions = \app\models\Expense::getSplittingOptions();
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
                'attribute'=>'title',
                'format'=>'html',
                'value'=>Html::tag('h3', $model->title),
            ],
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
                'value' => Yii::$app->formatter->asCurrency($model->amount, 'EUR'),
            ],
            [
                'attribute'=>'splitting',
                'value'=>$splittingOptions[$model->splitting],
            ],
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
