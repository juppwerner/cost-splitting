<?php

use yii\helpers\Html;
use \yii\helpers\Json;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Listfilter */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'List Filters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

list($controller, $action)=explode('/', $model->route);
?>
<div class="listfilter-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-search"></span> ' . Yii::t('app', 'Apply'), ['apply', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-remove"></span> ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
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
            // 'id',
            'sortorder',
            [
                'attribute'=>'name',
                'format'=>'html',
                'value'=>'<strong>'.$model->name.'</strong>',
            ],
            'route',
            [
                'attribute'=>'filterState',
                'format'=>'ntext',
                'value' => Json::encode(Json::decode($model->filterState), JSON_PRETTY_PRINT)
            ],
        ],
    ]) ?>

</div>
