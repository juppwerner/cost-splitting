<?php

use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\web\View;
use yii\web\YiiAsset;

use app\components\Html;
use app\dictionaries\Ewf;
use app\models\Expense;
// use app\widgets\GridView;


/** @var yii\web\View $this */
/** @var app\models\Costproject $model */
/** @var string $paypalClientId PayPal client id */
/** @var mixed $paymentOptions Array of payment rates */
/** @var string $currencyCode */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id'=>$model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Payment');

YiiAsset::register($this);
?>
<script src="https://www.paypal.com/sdk/js?client-id=<?= $paypalClientID ?>&currency=<?= $currencyCode ?>"></script>
<div class="costproject-checkout">

    <h1><?= Yii::t('app', 'Cost Project: {title}', ['title' => Html::encode($this->title)]) ?></h1>

    <p><?= Yii::t('app', 'In order to see the cost breakdown, a small payment ist required.') ?></p>
    <p>
        <?= Yii::t('app', 'Please select one of the following payment rates.') ?><br>
        <?= Yii::t('app', 'There are rates available either based on quantities, or rates based on time periods.') ?>
    </p>

    <form class="mb-4">
        <?php $n=1; foreach($paymentOptions as $n=>$paymentOption) : $paymentOption->loadTranslations(Yii::$app->language); ?>
        <div class="list-group" role="tablist" id="paymentOptions">
            <?php $rateType = Yii::t('app', 'Quantity-based'); if($paymentOption->type==='time') $rateType = Yii::t('app', 'Time-based'); ?>
            <div class="form-check list-group-item list-group-item-action" style="cursor:pointer">
                &nbsp;&nbsp;<input class="form-check-input" type="radio" name="paymentOption" id="paymentOption<?= $n ?>" value="<?= $paymentOption->sku ?>"<?= $n==0    ? ' checked' : '' ?>>
                <label class="form-check-label" for="exampleRadios<?= $n ?>">
                <span class="badge badge-secondary"><?= $rateType ?></span> <?= $paymentOption->translation->name ?> - <span class="badge badge-primary"><?= Yii::$app->formatter->asCurrency($paymentOption->amount, $currencyCode) ?></span>
                </label>
                <?php if(isset($paymentOption->description)) : ?>
                <small id="paymentOption<?= $n ?>HelpBlock" class="form-text text-muted">
                <?= $paymentOption->translation->description ?></small><?php endif; ?> 
            </div>
        </div>
        <?php $n++; endforeach; ?>
    </form>
    <p><?= Yii::t('app', 'Please select one of the following payment options:') ?></p>


    <div id="paypal-button-container"></div>
    <div id="result-message"></div>
</div>

<script>
var costproject_id=<?= $model->id ?>;
var returnUrl = '<?= Url::toRoute(['breakdown', 'id'=>$model->id, 'pay-ok'=>1], $schema = true) ?>';
var currencyCode = '<?= $currencyCode ?>';
window.paypal.Buttons(
    {
        async onInit() {
            $(".spinner-border").hide();
        },
        async createOrder() {
            try {
                const response = await fetch("<?= Url::to(['paypal/orders'], true) ?>", {
                        method: "POST",
                        headers: {
                        "Content-Type": "application/json",
                    },
                    // use the "body" param to optionally pass additional order information
                    // like product ids and quantities
                    body: JSON.stringify({
                        cart: [
                            {
                                paymentOptionId: $("input:radio[name='paymentOption']:checked").val(),
                                costprojectId: costproject_id,
                                quantity: 1,
                            },
                        ],
                    }),
                });
                
                const orderData = await response.json();
                    
                if (orderData.id) {
                    return orderData.id;
                } else {
                    const errorDetail = orderData?.details?.[0];
                    const errorMessage = errorDetail
                        ? `${errorDetail.issue} ${errorDetail.description} (${orderData.debug_id})`
                        : JSON.stringify(orderData);
                
                    throw new Error(errorMessage);
                }
            } catch (error) {
                console.error(error);
                resultMessage(`<h4><?= Yii::t('app', 'Error') ?></h4><?= Yii::t('app', 'Could not initiate PayPal Checkout...') ?><br><br>${error}`, true);
            }
        },
        async onApprove(data, actions) {
            try {
                const response = await fetch("<?= Url::to(['paypal/orders'], true) ?>/"+data.orderID+"/capture", {
                    method: "POST",
                    headers: {
                    "Content-Type": "application/json",}
                    ,}
                );
                const orderData = await response.json();
                // Three cases to handle:
                //   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
                //   (2) Other non-recoverable errors -> Show a failure message
                //   (3) Successful transaction -> Show confirmation or thank you message
                
                const errorDetail = orderData?.details?.[0];
                
                if (errorDetail?.issue === "INSTRUMENT_DECLINED") {
                    // (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
                    // recoverable state, per https://developer.paypal.com/docs/checkout/standard/customize/handle-funding-failures/
                    return actions.restart();
                } else if (errorDetail) {
                    // (2) Other non-recoverable errors -> Show a failure message
                    throw new Error(`${errorDetail.description} (${orderData.debug_id})`);
                } else if (!orderData.purchase_units) {
                    throw new Error(JSON.stringify(orderData));
                } else {
                    // (3) Successful transaction -> Show confirmation or thank you message
                    // Or go to another URL:  actions.redirect('thank_you.html');
                    const transaction =
                        orderData?.purchase_units?.[0]?.payments?.captures?.[0] ||
                        orderData?.purchase_units?.[0]?.payments?.authorizations?.[0];
                    resultMessage(
                        `Transaction ${transaction.status}: ${transaction.id}`,
                    );
                    window.location.href = returnUrl;
                    // console.log(
                    //     "Capture result",
                    //     orderData,
                    //     JSON.stringify(orderData, null, 2),
                    // );
                }
            } catch (error) {
                console.error(error);
                resultMessage(
                    `Sorry, your transaction could not be processed...<br><br>${error}`,
                );
            }
        }
    }
).render("#paypal-button-container");

function resultMessage(message, error=false) {
    const container = document.querySelector("#result-message");
    container.innerHTML = message;
    if(error==true) {
        $("#result-message").addClass('alert').addClass('alert-warning');
    }    
}

</script>

<?php $this->registerJs("
// var radios = $('input[type=\"radio\"][name=\"paymentOption\"]');
$('#paymentOptions div.list-group-item').on('click', function(event) {
    $(this).children('input[type=\"radio\"]').first().prop('checked', true);
    
});", VIEW::POS_READY
); ?>