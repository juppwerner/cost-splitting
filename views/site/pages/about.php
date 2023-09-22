<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

// Don't use fluid container
$this->context->fluid = false;

$this->title = Yii::t('app', 'About {appName}', ['appName'=>Yii::$app->name]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <?php if($this->context->action->id=='page') $tag='h1'; else $tag = 'h2'; ?>
    <?= Html::tag($tag, $this->title) ?>

    <p>
        This application allows managing of costs/expenses in projects.</p>
    <p>
        Expenses can be summarized by project.<br>
        Expenses are captured with the persons who payed them.<br>
        A cost breakdown can calculate which participant owes the other some amounts. 
    </p>
    <?php if($this->context->action->id=='page') : ?>
    <p>
        <?= Yii::t('app', 'This project was inspired by the article about Haushaltsbuch apps in the c\'T magazine 19/2022.') ?>
        <?= Yii::t('app', 'See <a href="{link}" target="_blank">here</a>.', ['link'=>'https://www.heise.de/select/ct/2022/19/2217110544762309952']) ?>
    </p>
    <?php endif; ?>
</div>
<?php $this->title = strip_tags($this->title); ?>
