<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\exchangerate\models\Exchangerate $model */

$this->title = Yii::t('exchangerate', 'Update Exchangerate: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('exchangerate', 'Exchangerates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('exchangerate', 'Update');
?>
<div class="exchangerate-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
