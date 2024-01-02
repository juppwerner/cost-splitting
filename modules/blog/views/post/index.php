<?php

use yii\bootstrap4\Nav;
use app\components\Html;
use yii\helpers\Url;
use yii\web\View;
// use app\widgets\GridView;
use yii\widgets\ListView;
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

$labels = [
    Post::STATUS_DRAFT      => ['class'=>'warning', 'label' => Yii::t('app','Draft')],
    Post::STATUS_PUBLISHED  => ['class'=>'success', 'label' => Yii::t('app','Published')],
    Post::STATUS_ARCHIVED   => ['class'=>'default', 'label' => Yii::t('app','Archived')],
];
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

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'list-group'],
        'itemOptions' => function ($model, $key, $index, $widget) {
            return [
                'tag' => 'a',
                'class' => 'list-group-item list-group-item-action', 
                'href' => Url::to(['view', 'id' => $model->id])
            ];
        },
        'itemView' => function($model, $key, $index, $widget) use($labels) {
            return 
                // \yii\helpers\VarDumper::dumpAsString($model->attributes, 10, true) .
                Html::tag(
                    'div',
                    Html::tag('h5', Html::encode($model->title), ['class' => 'mb-1']) 
                    . Html::tag('div', $model->creator->username.' | '.Yii::$app->formatter->asDate($model->updated_at, 'medium')) 
                    // . Html::tag('span', Yii::t('app', '{n,plural,=0{No expenses} =1{one expense} other{# expenses}}', ['n' => count($model->expenses)]), ['class' => 'badge badge-primary badge-pill pt-2']),
                    ,
                    ['class' => 'd-flex w-100 justify-content-between']
                )
                . Html::tag('div', Yii::$app->formatter->asMarkdown(Html::encode($model->intro)), ['class' => 'mb-1', 'style'=>'font-size: smaller'])
                . Html::tag('span', $labels[$model->status]['label'], ['class' => 'badge badge-' . $labels[$model->status]['class']])
                ;
                /*
                . Yii::$app->user->can('blogAuthor') 
                    ? Html::a(Html::icon('edit'), ['/blog/post/update', 'id'=>$model['id']], ['class'=>'btn btn-success btn-xs'])
                    : ''
                ;
                */
        },
    ]) ?>
</div>

<?php $this->registerJs("
$('#clearFiltersBtn').on('click', function() { 
    $('#clear-filters-form').submit();
});
", View::POS_READY, 'my-button-handler' ); ?>
