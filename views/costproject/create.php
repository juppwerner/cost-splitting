<?php

use yii\bootstrap4\Alert;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Costproject $model */

$this->title = Yii::t('app', 'Create New Cost Project');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="costproject-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Alert::widget([
        'options' => [
            'class' => 'alert-info',
        ],
        'body' => Yii::t('app', 'Enter the data for your new cost project.').'<br>'
            .Yii::t('app', 'When it was saved, you can continue with adding expenses to the project.')
    ]); ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
