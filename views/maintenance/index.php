<?php

/** @var yii\web\View $this */

use yii\bootstrap4\Alert;
use app\components\Html;

$this->title = Yii::t('app', 'Site Maintenance');
$this->params['breadcrumbs'][] = $this->title;
$this->context->fluid = false;

// Get maintenance parameters

// Bootstrap Alert CSS class:
$alertClass='info';
if(array_key_exists('maintenance.alertClass', Yii::$app->params))
    $alertClass = Yii::$app->params['maintenance.alertClass'];

// Alert message
$message=Yii::t('app', 'This site is currently in maintenance mode.');
if(array_key_exists('maintenance.message', Yii::$app->params))
    $message = Yii::$app->params['maintenance.message'];
?>
<div class="site-maintenace">
    <h1><?= $this->title ?></h1>
    <div id="message">
        <?= Alert::widget([
            'options' => [
                'class' => 'alert-'.$alertClass,
            ],
            'body' => $message,
            ]) ?>
    </div>
</div>