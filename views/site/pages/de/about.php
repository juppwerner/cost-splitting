<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

// Don't use fluid container
$this->context->fluid = false;

$this->title = Yii::t('app', 'About <i>{appName}</i>', ['appName'=>Yii::$app->name]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <?php if($this->context->action->id=='page') $tag='h1'; else $tag = 'h2'; ?>
    <?= Html::tag($tag, $this->title) ?>

    <p>
        Diese Anwendung erlaubt das Verwalten von Kosten/Ausgaben in Projekten.</p>
    <p>
        Ausgaben können in Projekten aufsummiert werden.<br>
        Ausgaben werden den Personen zugeordnet, die bezahlt haben und denen, die profitiert haben.<br>
        Die Kostenaufteilung kann berechnen, welcher Teilnehmer dem anderen welche Beträge schuldet.
    </p>
    <?php if($this->context->action->id=='page') : ?>
    <p>
        <?= Yii::t('app', 'This project was inspired by the article about Haushaltsbuch apps in the c\'T magazine 19/2022.') ?>
        <?= Yii::t('app', 'See <a href="{link}" target="_blank">here</a>.', ['link'=>'https://www.heise.de/select/ct/2022/19/2217110544762309952']) ?>
    </p>
    <?php endif; ?>
</div>

<?php $this->title = strip_tags($this->title); ?>