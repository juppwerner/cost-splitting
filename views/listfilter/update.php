<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Listfilter */

$this->title = Yii::t('app', 'Update List Filter');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Listfilters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="listfilter-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
