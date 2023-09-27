<?php
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>
Hello,

<?= Yii::t('app', 'You have been added to the cost project {title} on {appName}.', ['title'=>$costproject->title, 'appName' => Yii::$app->name]) ?>

<?= Yii::t('app', 'Link to Project:') ?>

<?= Url::to(['/costproject/view', 'id'=>$model->costprojectId], true) ?>
