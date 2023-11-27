<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;

use kartik\select2\Select2;
use kartik\typeahead\TypeaheadBasic;

use app\components\Html;
use app\dictionaries\CurrencyCodesDictEwf;
use app\models\Expense;

/** @var yii\web\View $this */
/** @var app\models\Expense $model */
/** @var yii\bootstrap4\ActiveForm $form */

// Prepare currency codes
$currencyCodes = CurrencyCodesDictEwf::allByLabel();

$costproject = $model->costproject;
?>

<div class="expense-form">

    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'costprojectId')->dropDownList(ArrayHelper::map($costprojects, 'id', 'title'), ['autofocus'=>'autofocus', 'prompt'=>Yii::t('app', '--- Select ---')])->hint(Yii::t('app','Select the cost project into which this expense falls')) ?>

    <?= $form->field($model, 'expenseType')->dropdownList(\app\dictionaries\ExpenseTypesDict::all(), ['prompt'=>Yii::t('app', '(Select)')]) ?>

    <?= '' // $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->widget(TypeaheadBasic::class, [
        'data' => $titles,
        'dataset' => ['limit' => 10],
        'options' => ['placeholder' => Yii::t('app', 'Filter as you type ...')],
        'pluginOptions' => ['highlight'=>true, 'minLength' => 2],
    ])->hint(Yii::t('app', 'e.g. Accommodation, Restaurant, Drinks')); ?>

    <?= $form->field($model, 'itemDate')->input('date') ?>

    <?= $form->field($model, 'amount', [
        'inputTemplate' => '<div class="input-group">{input}<div class="input-group-append">
            <span class="input-group-text">'.(!empty($costproject) ? $costproject->currency : '').'</span>
        </div></div>',
    ])->textInput(['maxlength' => true])->input('number', ['step'=>'.01']) ?>

    <?php if(!empty($costproject) && (bool)$costproject->useCurrency===true) : ?>

    <?= $form->field($model, 'currency')->widget(Select2::class, [
        'data' => $currencyCodes,
        // 'language' => 'de',
        'options' => ['placeholder' => Yii::t('app', 'Select a currency ...')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'exchangeRate')->textInput(['maxlength' => true])->input('number', ['step'=>'.000001'])->hint(Yii::t('app', 'Will be set when a currency is selected')) ?>

    <?php else : ?>
    
    <?= $form->field($model, 'currency')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'exchangeRate')->hiddenInput()->label(false) ?>
    
    <?php endif; ?>

    <?php if(is_null($participants)) : ?>
    <?= $form->field($model, 'payedBy')->textInput(['maxlength' => true])->hint(Yii::t('app', 'Press ENTER to show all participants')) ?>
    <?php else : ?>
    <?= $form->field($model, 'payedBy')->widget(Select2::class, [
        'data' => $participants,
        'options' => ['placeholder' => Yii::t('app', 'Select a participant ...')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->hint(Yii::t('app', 'Press ENTER to show all participants')); ?>
    <?php endif; ?>

    <?= $form->field($model, 'splitting')->radioList(Expense::getSplittingOptions(), ['separator'=>'<br>']) ?>

    <?=$form->field($model, 'participants')->widget(Select2::class, [
        'data' => $participants,
        'options' => ['placeholder' => Yii::t('app', 'Select one or more recipients ...'), 'multiple' => true],
        'pluginOptions' => [
            'tags' => true,
            'tokenSeparators' => [',', ' '],
            'maximumInputLength' => 10
        ],
    ]) ?>

    <?= '' // $form->field($model, 'splitting_weights')->textarea() ?>

    <?php if(!empty($model->splitting_weights) && substr($model->splitting_weights, 0, 1)=='{' and substr($model->splitting_weights, -1)=='}') {
        $weights = \yii\helpers\Json::decode($model->splitting_weights);
        $weights[Yii::t('app', '(add participant)')] = 0;
        // var_dump($weights);
    } else {
        $weights = [Yii::t('app', '(add participant)') => 0];  
    } ?>
    <div class="form-group field-expense-splitting_weights">
        <table>
            <thead>
                <tr>
                    <th><?= Yii::t('app', 'Participant') ?></th>
                    <th><?= Yii::t('app', 'Distribution') ?></th>
                    <th><?= Yii::t('app', 'Action') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $n=0; foreach($weights as $participant=>$weight) : ?>
                <tr class="participant_row_<?= $n ?>">
                    <td><input type="text" name="Expense[splitting_weights][<?= $n ?>][participant]" class="form-control" value="<?= $participant ?>"></td>
                    <td><input type="text" name="Expense[splitting_weights][<?= $n ?>][weight]" class="form-control text-center" value="<?= $weight ?>"></td>
                    <td class="text-center"><a class="deleteRow" href="#" data-id="<?= $n ?>" title="<?= Yii::t('app', 'Delete row') ?>">X</a></th>
                </tr>
                <?php $n++; endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= $form->field($model, 'documents')->widget(app\components\FileInputWidget::class) ?>

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

    var requestURL = '".Url::to(['/exchangerate/default/api'], true)."';
    requestURL += '?currencyCode=' + base + '&date=' + date;
    var request = new XMLHttpRequest();
    request.open('GET', requestURL);
    request.responseType = 'json';
    request.send();

    request.onload = function() {
        var response = request.response;
        console.log(response);
        if(!('exchangeRate' in response)) {
            alert('No exchange rate available for currency: ' + base)
        } else {
            $('#expense-exchangerate').val(response.exchangeRate);
        }
    }
});
$('input[type=radio][name=\"Expense[splitting]\"]').change(function() {
    if(this.value==='SELECTED' || this.value==='SELECTED_CUST') {
        toggleFieldExpenseParticipants(true);
    } else {
        toggleFieldExpenseParticipants(false);
    }
    if(this.value==='SELECTED_CUST') {
        toggleFieldExpenseSplittingWeights(true);
    } else {
        toggleFieldExpenseSplittingWeights(false);
    }
});
function toggleFieldExpenseParticipants(show=true) {
    if(show===true) {
        $('div.form-group.field-expense-participants').show();

    } else {
        $('div.form-group.field-expense-participants').hide();
    }
}
toggleFieldExpenseParticipants(".(in_array($model->splitting, ['SELECTED', 'SELECTED_CUST']) ? 'true' : 'false').");

function toggleFieldExpenseSplittingWeights(show=true) {
    if(show===true) {
        $('div.form-group.field-expense-splitting_weights').show();
    } else {
        $('div.form-group.field-expense-splitting_weights').hide();
    }
}
toggleFieldExpenseSplittingWeights(".(in_array($model->splitting, ['SELECTED_CUST']) ? 'true' : 'false').");

$('#expense-amount, #expense-exchangerate').on('mousewheel',
    function (event) {
        this.blur()
    }
);

$('.deleteRow').on('click', function(event) {
    event.preventDefault();
    alert($(this).data('id'));
    $('tr.participant_row_'+$(this).data('id')).remove();
});

    ",
    yii\web\View::POS_READY,
    'amount-change'
); ?>
