<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\Button;
use yii\bootstrap\Nav;
use yii\web\View;

// use yii\grid\GridView;
// use kartik\grid\GridView;
use \app\widgets\Gridview;
use kartik\editable\Editable;

use app\modules\lookup\models\Lookup;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lookup','Lookups');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.'Create Lookup', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php echo Nav::widget([
        'items' => [
            [ 'label' => '<span class="glyphicon glyphicon-plus"></span>'.' '.Yii::t('lookup', 'Create Lookup'), 'url' => ['create'] ],
            [ 'label' => '<span class="glyphicon glyphicon-refresh"></span>'.' '.Yii::t('app', 'Clear Filters'), 'url' => '#', 'linkOptions'=>['id'=>'clearFiltersBtn'] ],
        ],
        'encodeLabels' => false,
        'options' => ['class' => 'nav-pills alert alert-info action-buttons'],
    ]); ?>

     <?php // {{{ Clear Filters Form
    $form = Html::beginForm(['index'], 'post', ['id'=>'clear-filters-form', 'style'=>'display:inline']);
    $form .= Html::hiddenInput('clear-state', '1');
    $form .= Html::hiddenInput('redirect-to', '');
    $form .= Html::endForm();
    echo $form; /* }}} */ ?>

    <?= GridView::widget([
        'as filterBehavior' => \thrieu\grid\FilterStateBehavior::className(),
        'dataProvider' => $dataProvider,
        'filterModel' => $lookupModel,
        'tableOptions' => ['class'=>'table table-hover table-striped table-condensed'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\ActionColumn', 
                'template' => '{view}&nbsp;{update}',
                'contentOptions' => [ 'class' => 'text-center' ],
            ],

            //'id',
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'type',
                'editableOptions'=> [
                    'header'=>'Type',
                ],
                'filter'=>ArrayHelper::map(Lookup::find()->orderBy('type')->asArray()->all(), 'type', 'type'),
            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'sort_order',
                'contentOptions' => ['class'=>'text-center'],
                'editableOptions'=> [
                    'header'=>'Sort Order',
                ],

            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'name',
                'editableOptions'=> [
                    'header'=>'Name',
                ],

            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'code',
                'editableOptions'=> [
                    'header'=>'Code',
                ],

            ],
            //'comment:ntext',
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => 'active',
                'contentOptions' => ['class'=>'text-center'],
                'value' => function($model, $key) {
                    return ($model->active == '1') ? 'Yes' : 'No';
                },
                'filter' => ['1'=>'Yes', '2' => 'No'],
                'editableOptions'=> function ($model, $key, $index, $widget) {
                    $values = ['1'=>'Yes', '2' => 'No'];
                    return [
                        'header' => 'Active',
                        'attribute' => 'active',
                        'size' => 'sm',
                        'inputType' => Editable::INPUT_DROPDOWN_LIST,
                        'displayValueConfig' => $values,
                        'data' => $values,
                       //'formOptions'=> ['action' => ['/appt/editappt']] // point to new action
                    ];
                }
            ],
            // 'created_at',
            // 'created_by',
            // 'updated_by',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
        ],
    ]); ?>

</div>

<?php $this->registerJs("
$('#clearFiltersBtn').on('click', function() { 
    $('#clear-filters-form').submit();
});
", View::POS_READY, 'my-button-handler' ); ?>
