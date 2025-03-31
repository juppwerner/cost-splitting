<?php
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use app\components\Html;

/** @var yii\web\View $this */
/** @var app\models\forms\UploadCostprojectForm $model */
/** @var yii\bootstrap4\ActiveForm $form */

$this->title = Yii::t('app', 'Import Cost Project');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="upload-costproject-form">

    <h1><?= $this->title ?></h1>

    <p><?= Yii::t('app', 'Import a costproject including expenses and documents.') ?></p>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'exportFile')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Html::icon('upload') . Yii::t('app', 'Import'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Html::icon('x-square') . Yii::t('app', 'Cancel'), Url::previous('cost-project'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end() ?>

</div>