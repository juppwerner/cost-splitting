<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Listfilter */

$this->title = Yii::t('app', 'Create List Filter');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Listfilters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="listfilter-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
