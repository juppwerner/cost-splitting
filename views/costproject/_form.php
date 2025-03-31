<?php

use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;

use kartik\select2\Select2;

use app\components\Html;
use app\dictionaries\CurrencyCodesDictEwf;

/** @var yii\web\View $this */
/** @var app\models\Costproject $model */
/** @var yii\bootstrap4\ActiveForm $form */
?>

<div class="costproject-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'autofocus'=>'autofocus']) ?>
    <?= $form->field($model, 'participants')->textArea(['rows' => 3])->hint(Yii::t('app', 'Enter one participant per line') . ' | ' . Html::a(Yii::t('app', 'Sort Participants'), '#', ['id' => 'sort-participants-btn', /* 'class' => 'btn btn-primary btn-sm' */ ])) ?>
    <?= $form->field($model, 'sortParticipants')->checkbox()->hint(Yii::t('app', 'Sort participants in select lists by name')) ?>
    <?= $form->field($model, 'replaceNames')->textArea(['rows' => 3])->hint(Yii::t('app', 'Enter an object of names to be rolled up to another name')) ?>
        <?= $form->field($model, 'currency')->widget(Select2::class, [
        'data' => CurrencyCodesDictEwf::allByLabel(),
        // 'language' => 'de',
        'options' => ['placeholder' => Yii::t('app', 'Select a currency ...')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->hint(Yii::t('app', 'Select the project currency')); ?>
    <?= $form->field($model, 'useCurrency')->checkbox()->hint(Yii::t('app', 'Check to capture expenses using foreign currencies')) ?>
    <?= $form->field($model, 'description')->textarea()->hint(Yii::t('app', 'You may use Mardown Extra here')) ?>
    
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
$('#sort-participants-btn').on('click', function (ev) {
    ev.preventDefault();
    var str = document.getElementById('costproject-participants').value; 
    str = str.replace(/\\r?\\n/g, '\\r').replace(/\\r/g, '\\n').split('\\n').sort().join('\\n');
    document.getElementById('costproject-participants').value = str; 
    return false;
});
",
    yii\web\View::POS_READY,
    'usecurrency-change'
); ?>
