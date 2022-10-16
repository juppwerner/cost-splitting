<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

// Don't use fluid container
$this->context->fluid = false;

$this->title = Yii::t('app', 'About {appName}', ['appName'=>Yii::$app->name]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Yii::t('app', 'This application allows managing of costs/expenses in projects.') ?><br>
        <?= Yii::t('app', 'Expenses can be summarized by project.') ?><br>
        <?= Yii::t('app', 'Expenses are captured with the persons who payed them.') ?><br>
        <?= Yii::t('app', 'A cost breakdown can calculate which participant owes the other some amounts.') ?> 
    </p>
</div>
