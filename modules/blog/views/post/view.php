<?php

use yii\bootstrap4\Html;
use yii\helpers\Markdown;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Post */

// Get user roles
$isBlogAuthor = Yii::$app->user->can('blogAuthor');

$this->title = $model->recordName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->context->leftMenu = [
    [
        'label' => Yii::t('app', 'Operations'),
        'items' => [
            [ 'label' => Yii::t('app', 'Update'), 'url' => ['update', 'id'=>$model->id], 'icon'=>'pencil'],
        ],
    ],
];

?>
<div class="post-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div style="font-weight:bold;font-size:larger">
        <?= str_replace('<table>', '<table class="table table-striped">', Markdown::process(str_replace([".\r\n", "!\r\n"], [".  \r\n", "!  \r\n"], $model->intro), 'extra')) ?>
    </div>

    <p>
        <?= str_replace('<table>', '<table class="table table-striped">', Markdown::process(str_replace([".\r\n", "!\r\n"], [".  \r\n", "!  \r\n"], $model->content), 'extra')) ?>
    </p>

    <hr>

    <h4><?= Yii::t('app','Other Infos') ?></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'status',
                'value' => $model->statusOptions[$model->status],
            ],
            'id',
        ],
    ]) ?>

    <!-- {{{ History -->
    <h4><?= Yii::t('app', 'History') ?></h4>
    <?= DetailView::widget([
       'model' => $model,
       'attributes' => [
           ['attribute'=>'created_at', 'format'=>'html', 'value'=>Yii::$app->formatter->asDateTime($model->created_at)],
           ['attribute'=>'createUserName', 'format'=>'html'],
           ['attribute'=>'updated_at', 'format'=>'html', 'value'=>Yii::$app->formatter->asDateTime($model->updated_at)],
           ['attribute'=>'updateUserName', 'format'=>'html'],
       ],
    ]) ?>
    <!-- }}} -->

    <?php if($isBlogAuthor) : /* {{{ DELETE BTN */ ?>     <p>
        <?= Html::a('<i class="fas fa-trash"></i>&nbsp;', ['delete', 'id' => $model->id], [
            'title' => Yii::t('app','Click to confirm deleting post: {recordName}', ['recordName'=>$model->recordName]),
            'class' => 'btn btn-danger btn-xs hidden-print',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php endif; /* }}} */ ?>

</div>

<style type="text/css">
<!--
.post-view h1 {
    margin-top: 40px;
}
.post-view hr {
    margin-bottom: 80px;
}
-->
</style>
