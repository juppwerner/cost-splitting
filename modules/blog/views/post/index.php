<?php

use yii\bootstrap4\Nav;
use yii\helpers\Html;
use yii\web\View;
use app\widgets\GridView;
use app\modules\blog\models\Post;
    
/* @var $this yii\web\View */
/* @var $searchModel app\modules\blog\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

// Get user roles
$isBlogAuthor = Yii::$app->user->can('blogAuthor');

$this->title = Yii::t('app', 'Posts');
$this->params['breadcrumbs'][] = $this->title;
/* $this->context->leftMenu = [ // {{{ 
    [
        'label' => Yii::t('app', 'Operations'),
        'items' => [
            [ 'label' => Yii::t('app', 'New Post'), 'url' => ['create'], 'icon'=>'plus'],
            [ 'label' => Yii::t('app', 'Clear Filters'), 'url' => '#', 'bodyOptions'=>['id'=>'clearFiltersBtn'], 'icon'=>'refresh' ],
        ],
    ],
]; */ // }}} 
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo Nav::widget([
        'items' => [
            [ 'label' => '<i class="fas fa-plus"></i>'.' '.Yii::t('app', 'New Post'), 'url' => ['create'], 'visible'=>$isBlogAuthor],
            [ 'label' => '<i class="fas fa-redo"></i>'.' '.Yii::t('app', 'Clear Filters'), 'url' => '#', 'linkOptions'=>['id'=>'clearFiltersBtn'] ],
            [ 'label' => '<i class="fas fa-filter"></i>'.' '.Yii::t('app', 'Save Filter Set'), 'url' => ['launch/save-filters', 'gridId'=>'', 'searchModel'=>'LaunchSearch'], 'linkOptions'=>['id'=>'saveFiltersBtn'] ],
            [ 'label' => '<i class="fas fa-th-list"></i>'.' '.Yii::t('app', 'All Saved Filter Sets'), 'url' => ['listfilter/index', 'ListfilterSearch[route]'=>'post/index'], 'linkOptions'=>['id'=>'listFiltersBtn'] ],
        ],
        'encodeLabels' => false,
        'options' => ['class' => 'nav-pills alert alert-info action-buttons'],
    ]);
    ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php // {{{ Clear Filters Form ?>
    <?php  $form = Html::beginForm(['index'], 'post', ['id'=>'clear-filters-form', 'style'=>'display:inline']); ?>
    <?php  $form .= Html::hiddenInput('clear-state', '1'); ?>
    <?php  $form .= Html::hiddenInput('redirect-to', ''); ?>
    <?php  $form .= Html::endForm(); ?>
    <?php  echo $form; /* }}} */ ?>

    <?= GridView::widget([ // {{{ 
        'dataProvider' => $dataProvider,
        // 'as filterBehavior' => \thrieu\grid\FilterStateBehavior::className(),
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'contentOptions' => [ 'class' => 'text-center' ],
            ],
            'id',
            [
                'attribute' => 'title',
                'format'    => 'html',
                'value'     => function ($data) {
                    return Html::tag('b', Html::a($data->title, ['view', 'id'=>$data->id], ['title' => Yii::t('app', 'View Post: {recordName}', ['recordName'=>$data->recordName])]));
                },
            ],
            [
                'attribute' => 'status',
                'filter' => Post::getStatusOptions(),
                'value' => function($data) {
                    return Post::getStatusOptions()[$data->status];
                }
            ],
            'intro:ntext',
            'content:ntext',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            //'created_by',
            //'updated_at',
            //'updated_by',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'contentOptions' => [ 'class' => 'text-center' ],
            ],
        ],
    ]); /* }}} */?>
</div>

<?php $this->registerJs("
$('#clearFiltersBtn').on('click', function() { 
    $('#clear-filters-form').submit();
});
", View::POS_READY, 'my-button-handler' ); ?>
