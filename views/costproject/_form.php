<?php

use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;

use kartik\select2\Select2;

use app\components\Html;
use app\dictionaries\CurrencyCodesDict;

/** @var yii\web\View $this */
/** @var app\models\Costproject $model */
/** @var yii\bootstrap4\ActiveForm $form */
?>

<div class="costproject-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'autofocus'=>'autofocus']) ?>
    <?= $form->field($model, 'participants')->textArea(['rows' => 4])->hint(Yii::t('app', 'Enter one participant per line')) ?>
    <?= $form->field($model, 'currency')->widget(Select2::classname(), [
        'data' => CurrencyCodesDict::all(),
        // 'language' => 'de',
        'options' => ['placeholder' => Yii::t('app', 'Select a currency ...')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->hint(Yii::t('app', 'Select the project currency')); ?>
    <?= $form->field($model, 'useCurrency')->checkbox()->hint(Yii::t('app', 'Check to capture expenses using foreign currencies')) ?>
    
    <div class="form-group">
        <?= Html::submitButton(Html::icon('save') . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Html::icon('x-square') . Yii::t('app', 'Cancel'), Url::previous('cost-project'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs("
var useCurrency = ".($model->useCurrency ? 'true' : 'false').";
// if(useCurrency==false)
//    $('.field-costproject-currency').hide();
/*
$('#costproject-usecurrency').on('change', function() {
    if($(this).is(':checked')) {
        $('.field-costproject-currency').show('slow');
    } else {
        $('.field-costproject-currency').hide('slow');
    }

});
*/
",
    yii\web\View::POS_READY,
    'usecurrency-change'
); ?>
