<?php

use yii\bootstrap4\ActiveForm;

use app\components\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Costproject $costproject */
/** @var app\models\forms\AddUserForm $model */

$this->title = $costproject->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);
?>
<div class="costproject-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Html::icon('eye') . Yii::t('app', 'View'), ['view', 'id' => $costproject->id], ['class' => 'btn btn-info']) ?>
        <?= Html::a(Html::icon('edit') . Yii::t('app', 'Update'), ['update', 'id' => $costproject->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Html::icon('file-text') . Yii::t('app', 'Cost Breakdown'), ['breakdown', 'id' => $costproject->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <!-- Cost Project Detail View -->
    <?= $this->render('_view', ['model' => $costproject]) ?>

    <h2><?= Yii::t('app', 'Add New User') ?></h2>

    <p><?= Yii::t('app', 'Add new users to this project.') ?><br>
        <?= Yii::t('app', 'The user must be already registered within this application.') ?><br>
        <?= Yii::t('app', 'After having saved, the newly assigned user will receive an email containing the link to this project.') ?></p>	

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model) ?>
    
    <?= Html::activeHiddenInput($model, 'costprojectId') ?>

    <?= $form->field($model, 'username')->textInput()->hint(Yii::t('app', 'Enter an existing username')) ?>

    <div class="form-group">
        <?= Html::submitButton(Html::icon('save') . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Html::icon('x-square') . Yii::t('app', 'Cancel'), Url::previous('cost-project'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
