<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Costproject $model */

$this->title = Yii::t('app', 'Create New Cost Project');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="costproject-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
