<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use kartik\select2\Select2;

use app\components\Html;
use app\dictionaries\CurrencyCodesDict;
use app\models\Costproject;
use app\models\Expense;

$participants = null;
if(!empty($model->costprojectId))
    $participants = Costproject::findOne(['id' => $model->costprojectId])->getParticipantsList();


if(!is_array($model->participants)) {
    if(empty($model->participants))
        $model->participants = array();
    else
        $model->participants = explode(';', $model->participants);
}
    
/** @var yii\web\View $this */
/** @var app\models\Expense $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="expense-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'costprojectId')->dropDownList(ArrayHelper::map(Costproject::find()->all(), 'id', 'title'), ['autofocus'=>'autofocus', 'prompt'=>Yii::t('app', '--- Select ---')])->hint(Yii::t('app','Select the cost project into which this expense falls')) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'expenseType')->dropdownList(\app\dictionaries\ExpenseTypesDict::all(), ['prompt'=>Yii::t('app', '(Select)')]) ?>

    <?= $form->field($model, 'itemDate')->input('date') ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true])->input('number', ['step'=>'.01']) ?>

    <?= $form->field($model, 'currency')->widget(Select2::classname(), [
        'data' => CurrencyCodesDict::all(),
        // 'language' => 'de',
        'options' => ['placeholder' => Yii::t('app', 'Select a currency ...')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'exchangeRate')->textInput(['maxlength' => true])->input('number', ['step'=>'.000001'])->hint(Yii::t('app', 'Will be set when a currency is selected')) ?>

    <?php if(is_null($participants)) : ?>
    <?= $form->field($model, 'payedBy')->textInput(['maxlength' => true]) ?>
    <?php else : ?>
    <?= $form->field($model, 'payedBy')->widget(Select2::classname(), [
        'data' => $participants,
        'options' => ['placeholder' => Yii::t('app', 'Select a participant ...')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
    <?php endif; ?>

    <?= $form->field($model, 'splitting')->radioList(Expense::getSplittingOptions(), ['separator'=>'<br>']) ?>

    <?=$form->field($model, 'participants')->widget(Select2::classname(), [
        'data' => $participants,
        'options' => ['placeholder' => Yii::t('app', 'Select one or more participants ...'), 'multiple' => true],
        'pluginOptions' => [
            'tags' => true,
            'tokenSeparators' => [',', ' '],
            'maximumInputLength' => 10
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Html::icon('save') . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Html::icon('x-square') . Yii::t('app', 'Cancel'), Url::previous('cost-project'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs("
$('#expense-currency').on('change', function() {
    var base = $('#expense-currency').val();
    var symbol = 'EUR';
    var date = $('#expense-itemdate').val() ;

    if(base==symbol)
        return;

    // alert(date + ': ' + $(this).val() + ' ' + symbol);

    var requestURL = 'https://api.exchangerate.host/';
    var request = new XMLHttpRequest();
    request.open('GET', requestURL + date + '?base=' + base + '&symbols=' + symbol);
    request.responseType = 'json';
    request.send();

    request.onload = function() {
        var response = request.response;
        console.log(response);
        if(!('rates' in response)) {
            alert('No exchange rate available for currency: ' + base)
        } else {
            $('#expense-exchangerate').val(response.rates[symbol]);
        }
    }
});
$('input[type=radio][name=\"Expense[splitting]\"]').change(function() {
    if(this.value==='SELECTED') {
        toggleFieldExpenseParticipants(true);
    } else {
        toggleFieldExpenseParticipants(false);
    }
});
function toggleFieldExpenseParticipants(show=true) {
    if(show===true) {
        $('div.form-group.field-expense-participants').show();
    } else {
        $('div.form-group.field-expense-participants').hide();
    }
}
toggleFieldExpenseParticipants(".($model->splitting==='SELECTED' ? 'true' : 'false').");
    ",
    yii\web\View::POS_READY,
    'amount-change'
); ?>
