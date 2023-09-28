<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>
<?= Yii::t('app', 'Hello,') ?>

<p><?= Yii::t('app', 'You have been removed from the cost project {title} on {appName}.', ['title'=>$costproject->title, 'appName' => Yii::$app->name]) ?></p>

<p><?= Yii::t('app', 'If you have any questions regarding this action, please contact the project owner {username}.', ['username'=>$costproject->createUserName]) ?></p>

<p><?= Yii::t('app', 'Link to Project:') ?><br>
<?= Url::to(['/costproject/view', 'id'=>$model->costprojectId], true) ?>
