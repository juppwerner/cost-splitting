<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\exchangerate\models\Exchangerate $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="exchangerate-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'histDate')->textInput() ?>

    <?= $form->field($model, 'currencyCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'exchangeRate')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('exchangerate', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
