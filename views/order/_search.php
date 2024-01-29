<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\search\OrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'userId') ?>

    <?= $form->field($model, 'purchaseType') ?>

    <?= $form->field($model, 'paymentOptionCode') ?>

    <?= $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'currency') ?>

    <?php // echo $form->field($model, 'paymentInfo') ?>

    <?php // echo $form->field($model, 'ordered_at') ?>

    <?php // echo $form->field($model, 'quantityRemaining') ?>

    <?php // echo $form->field($model, 'expiresAtTimestamp') ?>

    <?php // echo $form->field($model, 'isConsumed') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
