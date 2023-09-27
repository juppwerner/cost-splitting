<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Expense $model */

$this->title = Yii::t('app', 'Update Expense: {name}', [
    'name' => $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => $model->costproject->recordName, 'url' => ['costproject/view', 'id'=>$model->costprojectId]];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="expense-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model'         => $model,#
        'costprojects'  => $costprojects,
        'participants'  => $participants,
        'titles'        => $titles,
    ]) ?>

</div>
