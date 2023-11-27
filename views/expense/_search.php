<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\search\ExpenseSearch $model */
/** @var yii\bootstrap4\ActiveForm $form */
?>

<div class="expense-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= '' // $form->field($model, 'id') ?>

    <?= '' // $form->field($model, 'title') ?>

    <?= $form->field($model, 'costprojectId')->dropDownList(ArrayHelper::map($costprojects, 'id', 'title'), ['autofocus'=>'autofocus', 'prompt'=>Yii::t('app', '--- Select ---')]) ?>

    <?= '' // $form->field($model, 'itemDate') ?>

    <?= '' // $form->field($model, 'amount') ?>

    <?= '' // $form->field($model, 'splitting') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
