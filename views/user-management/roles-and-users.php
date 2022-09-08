<?php

use yii\bootstrap4\Nav;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Html;
use yii\web\View;
use yii\widgets\Pjax;

use \app\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChecklistItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Roles and Users');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users Admin'), 'url' => ['user/admin']];
$this->params['breadcrumbs'][] = $this->title;
/* {{{ $this->context->leftMenu = [
    [
        'label' => Yii::t('app', 'Operations'),
        'items' => [
            [ 'label' => Yii::t('app', 'New User'),         'url' => ['/user/admin/create'],    'icon'=>'plus'],
            [ 'label' => Yii::t('app', 'Roles'),            'url' => ['/rbac/role/index'],      'icon'=>'user'],
            [ 'label' => Yii::t('app', 'Clear Filters'),    'url' => '#',                       'icon'=>'refresh', 'bodyOptions'=>['id'=>'clearFiltersBtn'] ],
        ],
    ],
]; 
// }}} /
?>
<div class="checklist-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo Nav::widget([
        'items' => [
            [ 'label' => '<i class="fas fa-plus"></i>'.' '.Yii::t('app', 'New User'), 'url' => ['/user/admin/create']],
            [ 'label' => '<i class="fas fa-user"></i>'.' '.Yii::t('app', 'Users'), 'url' => ['/user/admin/index'] ],
            [ 'label' => '<i class="fas fa-user-tag"></i>'.' '.Yii::t('app', 'Roles'), 'url' => ['/user/role/index'] ],
            [ 'label' => '<i class="fas fa-redo"></i>'.' '.Yii::t('app', 'Clear Filters'), 'url' => '#', 'linkOptions'=>['id'=>'clearFiltersBtn'] ],
        ],
        'encodeLabels' => false,
        'options' => ['class' => 'nav-pills alert alert-info action-buttons'],
    ]);
    ?>

    <?php Pjax::begin(); ?>
     <?php // {{{ Clear Filters Form
    $form = Html::beginForm(['roles-and-users'], 'post', ['id'=>'clear-filters-form', 'style'=>'display:inline']);
    $form .= Html::hiddenInput('clear-state', '1');
    $form .= Html::hiddenInput('redirect-to', '');
    $form .= Html::endForm();
    echo $form; /* }}} */ ?>
    <?= GridView::widget([ // {{{ 
        'dataProvider' => $dataProvider,
        // 'as filterBehavior' => \thrieu\grid\FilterStateBehavior::className(),
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
                'attribute' => 'roleName',
                'format' => 'html',
                'filter'    => ArrayHelper::map(Yii::$app->authManager->getItems(),'name','name'),
                'value' => function($data) {
                    return Html::a($data['roleName'], ['rbac/role/update', 'name'=>$data['roleName']], ['title' => Yii::t('app', 'Update Role: {roleName}', ['roleName'=>$data['roleName']])]);
                }
            ],
            [
                'attribute' => 'roleDescription',
            ],
            'userFullName',
            [
                'attribute' => 'username',
                'format'=>'raw',
                'value' => function($data) {
                    return Html::a($data['username'], ['user/admin/update', 'id'=>$data['userId']], ['title'=>Yii::t('app', 'Update User: {username}', ['username'=>$data['username']])]);
                }
            ],
            'userEmail:email',
            [
                'class' => 'yii\grid\ActionColumn', // {{{
                'template' => '{update}',
                'contentOptions' => [ 'class' => 'text-center' ],
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', \yii\helpers\Url::to(['user/admin/assignments', 'id' => $model['userId']]), ['title'=>Yii::t('app', 'Update Assignment: {roleName} > {username}', ['roleName'=>$model['roleName'], 'username'=>$model['username']])] );
                    },
                ],
            ], // }}}
        ],
    ]); /* }}} */?>
</div>

<?php $this->registerJs("
$('#clearFiltersBtn').on('click', function() { 
    $('#clear-filters-form').submit();
});
", View::POS_READY, 'my-button-handler' ); ?>
