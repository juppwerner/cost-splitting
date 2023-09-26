<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\exchangerate\models\Exchangerate $model */

$this->title = Yii::t('exchangerate', 'Create Exchangerate');
$this->params['breadcrumbs'][] = ['label' => Yii::t('exchangerate', 'Exchange Rates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exchangerate-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
