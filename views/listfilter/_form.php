<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Listfilter */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= $model->isNewRecord ? '(New Item)' : $model->name ?></h3>
    </div>
    <div class="listfilter-form  panel-body">

        <?php $form = ActiveForm::begin([ 'layout' => 'horizontal' ] ); ?>

        <?= $form->errorSummary($model) ?> <!-- ADDED HERE -->

        <?= $form->field($model, 'name', ['inputOptions' => ['autofocus' => 'autofocus']])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sortorder')->textInput() ?> 

        <?= $form->field($model, 'route')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'filterState')->textarea(['rows' => 6]) ?>

        <div class="form-group">
            <label class="control-label col-sm-3"></label>
            <div class="col-sm-6">
                <?= Html::submitButton($model->isNewRecord ? '<span class="fas fa-save"></span> ' . Yii::t('app', 'Create') : '<span class="fas fa-save"></span> ' . Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::resetButton('<span class="fas fa-redo"></span> ' . Yii::t('app', 'Reset') , ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
