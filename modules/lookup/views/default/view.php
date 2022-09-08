<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Lookups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute'=>'type',
                'format'=>'html',
                'value'=>Html::a($model->type, ['index', 'LookupSearch[type]'=>$model->type]),
            ],
            'name',
            'name_de',
            'code',
            'comment:ntext',
            [
                'attribute' => 'active',
                'value' => ($model->active == '1') ? 'Yes' : 'No',
            ],
            'sort_order',
            'created_by',
            'updated_by',
            [
                'attribute' => 'created_at',
                'value' => Yii::$app->formatter->asDateTime($model->created_at),
            ],
            [
                'attribute' => 'updated_at',
                'value' => Yii::$app->formatter->asDateTime($model->updated_at),
            ],
        ],
    ]) ?>

</div>
