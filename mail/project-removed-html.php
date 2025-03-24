<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
/* @var $model \app\models\AddUserForm Form model to add/remove user to project */
/* @var $costproject \app\models\Costproject Cost project active record */

$appName = Yii::t('app', '_appName_');
$url = Url::to(['/costproject/view', 'id' => $model->costprojectId], true);
?>
<?= Yii::t('app', 'Hello,') ?>

<p><?= Yii::t('app', 'You have been removed from the cost project <b>{title}</b> on <i>{appName}</i>.', ['title'=>$costproject->title, 'appName' => $appName]) ?></p>

<p><?= Yii::t('app', 'If you have any questions regarding this action, please contact the project owner {username}.', ['username'=>$costproject->createUserName]) ?></p>

<p>
    <b><?= Yii::t('app', 'Link to Project:') ?></b><br>
    <a href="<?= $url ?>"><?= $url ?></a>
</p>

<?php // Greetings will be added by mail/layouts ?>
