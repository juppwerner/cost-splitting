<?php
use yii\bootstrap4\ActiveForm;
use app\components\Html;

/** @var yii\web\View $this */
/** @var app\modules\exchangerate\models\forms\UploadEzbDataForm $model */
/** @var yii\bootstrap4\ActiveForm $form */

$this->title = Yii::t('app', 'Import EZB Historic Currency Exchange Rates');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Exchange Rates'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= $this->title ?></h1>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'csvFile')->fileInput() ?>
    <?= $form->field($model, 'truncateTable')->checkbox() ?>
    <div class="form-group">
        <?= Html::submitButton(Html::icon('upload') . Yii::t('app', 'Import'), ['class' => 'btn btn-success']) ?>
    </div>
<?php ActiveForm::end() ?>