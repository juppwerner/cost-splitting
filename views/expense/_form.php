<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\models\Costproject;
use app\models\Expense;

/** @var yii\web\View $this */
/** @var app\models\Expense $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="expense-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'costprojectId')->dropDownList(ArrayHelper::map(Costproject::find()->all(), 'id', 'title'), ['autofocus'=>'autofocus', 'prompt'=>Yii::t('app', '--- Select ---')])->hint(Yii::t('app','Select the cost project into which this expense falls')) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'itemDate')->input('date') ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true])->input('number', ['step'=>'.01']) ?>

    <?= $form->field($model, 'payedBy')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'splitting')->radioList(Expense::getSplittingOptions()) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
