<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use kartik\select2\Select2;

use app\dictionaries\CurrencyCodesDict;

/** @var yii\web\View $this */
/** @var app\models\Costproject $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="costproject-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'autofocus'=>'autofocus']) ?>
    <?= $form->field($model, 'participants')->textArea(['rows' => 4])->hint(Yii::t('app', 'Enter one participant per line')) ?>
    <?= $form->field($model, 'useCurrency')->checkbox() ?>
    <?= $form->field($model, 'currency')->widget(Select2::classname(), [
        'data' => CurrencyCodesDict::all(),
        // 'language' => 'de',
        'options' => ['placeholder' => Yii::t('app', 'Select a currency ...')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
    
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Cancel'), Url::previous('cost-project'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs("
var useCurrency = ".($model->useCurrency ? 'true' : 'false').";
if(useCurrency==false)
    $('.field-costproject-currency').hide();
$('#costproject-usecurrency').on('change', function() {
    alert($(this).is(':checked'));
    if($(this).is(':checked')) {
        $('.field-costproject-currency').show();
    } else {
        $('.field-costproject-currency').hide();
    }

});
",
    yii\web\View::POS_READY,
    'usecurrency-change'
); ?>
