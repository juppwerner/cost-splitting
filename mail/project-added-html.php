<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>
<?= Yii:t('app', 'Hello,') ?>

<p><?= Yii::t('app', 'You have been added to the cost project {title} on {appName}.', ['title'=>$costproject->title, 'appName' => Yii::$app->name]) ?></p>

<p>Link to Project:<br>
<?= Url::to(['/costproject/view', 'id'=>$model->costprojectId], true) ?>
