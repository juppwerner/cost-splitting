<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Markdown;
use app\components\DDUtils;

// Don't use fluid container
$this->context->fluid = false;

$this->title = Yii::t('app','Revision History');
$this->params['breadcrumbs'][] = $this->title;

$markdown = file_get_contents(Yii::getAlias('@app/CHANGES.md'));

?>
<div class="site-changes">
    <?= Markdown::process($markdown, 'extra'); // use markdown extra ?>
</div>
