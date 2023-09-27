<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup */
/* @var $form yii\bootstrap4\ActiveForm */
?>

        <div class="lookup-form">

            <?php $form = ActiveForm::begin(['layout'=>'horizontal']); ?>

            <?= $form->field($model, 'type', ['inputOptions' => ['autofocus' => 'autofocus']])->textInput(['maxlength' => 255]) ?>

            <?= $form->field($model, 'code')->textInput(['maxlength' => 255]) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

            <?= $form->field($model, 'name_de')->textInput(['maxlength' => 255]) ?>

            <?= $form->field($model, 'comment')->textarea(['rows' => 2]) ?>

            <?php if ($model->isNewRecord) $model->active='1'; ?>
            
            <?= $form->field($model, 'active')->dropDownList(
                ['1'=>'Yes', '2' => 'No'],
                ['prompt'=>'--- Select ---'] 
            ) ?>

            <?= $form->field($model, 'sort_order')->textInput() ?>

            <?= $form->field($model, 'saveAsNew')->checkbox() ?>

            <div class="form-group">
                <label class="control-label col-sm-3"></label>
                <div class="col-sm-6">
                    <?= Html::submitButton($model->isNewRecord ? '<span class="glyphicon glyphicon-ok"></span> ' . Yii::t('app', 'Create') : '<span class="glyphicon glyphicon-ok"></span> ' . Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    <?= Html::resetButton('<span class="glyphicon glyphicon-fast-backward"></span> ' . Yii::t('app', 'Reset') , ['class' => 'btn btn-success']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>


<?php
$script = <<< JS
    
    // incident location
    $("#lookup-code").bind("blur", function() {
        var code = $(this).val() || 0;
        // $('#lookup-sort_order').val(code);

    });

JS;
$this->registerJs($script);

?>
