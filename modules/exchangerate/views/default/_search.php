<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\exchangerate\models\search\ExchangerateSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="exchangerate-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'histDate') ?>

    <?= $form->field($model, 'currencyCode') ?>

    <?= $form->field($model, 'exchangeRate') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('exchangerate', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('exchangerate', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
