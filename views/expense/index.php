<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;

use app\components\Html;
use app\models\Costproject;
use app\models\Expense;

/** @var yii\web\View $this */
/** @var app\models\search\ExpenseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Expenses');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['/costproject']];
if(!empty($_GET['ExpenseSearch']['costprojectId'])) {
    $costproject = Costproject::findOne((int)$_GET['ExpenseSearch']['costprojectId']);
    if(!empty($costproject))
        $this->params['breadcrumbs'][] = ['label' => $costproject->recordName, 'url' => ['costproject/view', 'id'=>$costproject->id]];
}
$this->params['breadcrumbs'][] = $this->title;

// Get an array of all participants for the filter dropdown:
$allParticipants = \app\models\Costproject::getAllParticipants();
$splittingOptions = \app\models\Expense::getSplittingOptions();
?>
<div class="expense-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Html::icon('plus-square') . Yii::t('app', 'Create Expense'), ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel, 'costprojects' => $costprojects]); ?>

    <?php if(!Yii::$app->mobileSwitcher->showMobile) : ?>

    <?php $dataProvider->pagination->pageSize = 10; ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class'=>'table table-striped table-responsive-sm table-hover'],
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => ActionColumn::class,
                'template' => '{view}&nbsp;{update}',
                'contentOptions' => [ 'class' => 'text-center text-nowrap' ],
                'urlCreator' => function ($action, Expense $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],

            [
                'attribute'=>'itemDate',
                'contentOptions' => [ 'class' => 'text-center' ],
                'value'=>function($data) {
                    return Yii::$app->formatter->asDate($data->itemDate, 'php:'.Yii::t('app', 'Y-m-d'));
                },
            ],
            [
                'attribute'=>'title',
                'format'=>'html',
                'value'=>function($data) {
                    return Html::a(Html::tag('strong', $data->title), ['view', 'id'=>$data->id], ['title'=>Yii::t('app', 'View expense: {title}', ['title'=>$data->title])]);
                },
            ],
            [
                'attribute'=>'amount',
                'contentOptions' => [ 'class' => 'text-right' ],
                'value' => function($data) {
                    return Yii::$app->formatter->asCurrency($data->amount, 'EUR');
                },
            ],
            [
                'attribute'=>'payedBy',
                'filter'=>$allParticipants,
            ],
            [
                'attribute'=>'splitting',
                'filter'=>$splittingOptions,
                'contentOptions'=>['style'=>'font-size:smaller'],
                'value'=>function($data) use($splittingOptions) {
                    return $splittingOptions[$data->splitting];
                },
            ],
            [
                'attribute'=>'costprojectId',
                'format'=>'html',
                'filter'=>ArrayHelper::map(Costproject::find()->innerJoinWith('users')->where(['user.id' => Yii::$app->user->id])->all(), 'id', 'title'),
                'value'=>function($data) {
                    return Html::a($data->costproject->title, ['costproject/view', 'id'=>$data->costprojectId], ['title'=>Yii::t('app', 'View cost project: {title}', ['title'=>$data->costproject->title])]);
                },
            ],
            // 'id',
            [
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, Expense $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php else : ?>

    <?= ListView::widget([ // {{{ 
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'list-group'],
        'itemOptions' => function ($model, $key, $index, $widget) {
            return [
                'tag' => 'a',
                'class' => 'list-group-item list-group-item-action', 
                'href' => Url::to(['view', 'id' => $model->id])
            ];
        },
        'itemView' => function ($model, $key, $index, $widget) {
            return 
                Html::tag(
                    'div',
                    Html::tag('h5', Html::encode($model->title), ['class' => 'mb-1']) 
                    . Html::tag('span', Yii::$app->formatter->asCurrency($model->amount, $model->currency), ['class' => 'pt-2 badge badge-primary badge-pill'])
                    ,
                    ['class' => 'd-flex w-100 justify-content-between']
                )
                . Html::tag('span', \app\dictionaries\ExpenseTypesDict::get($model->expenseType), ['class' => 'badge badge-'.($model->expenseType==\app\dictionaries\ExpenseTypesDict::EXPENSETYPE_EXPENSE ? 'primary' : 'secondary')] )
                . ' &ndash; '
                . Html::tag('small', Yii::t('app', 'Payed by {name}', ['name' => $model->payedBy]))
                ;
        },
    ]) /* }}} */ ?>

    <?php endif; ?>

    <?php Pjax::end(); ?>

</div>
