<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

// Don't use fluid container
$this->context->fluid = false;

$this->title = Yii::t('app', 'About <i>{appName}</i>', ['appName'=>Yii::$app->name]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= $this->title ?></h1>

    <p>
        Diese Anwendung erlaubt das Verwalten von Kosten/Ausgaben in Projekten.</p>
    <p>
        Ausgaben können in Projekten aufsummiert werden.<br>
        Ausgaben werden den Personen zugeordnet, die bezahlt haben und denen, die profitiert haben.<br>
        Die Kostenaufteilung kann berechnen, welcher Teilnehmer dem anderen welche Beträge schuldet.
    </p>
</div>

<?php // FOR TESTING ONLY: \yii\helpers\VarDumper::dump($_SERVER, 10, true); ?>
<?php $this->title = strip_tags($this->title); ?>