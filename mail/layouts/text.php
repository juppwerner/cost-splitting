<?php
use yii\helpers\Html;

/** @var \yii\web\View $this view component instance */
/** @var \yii\mail\MessageInterface $message the message being composed */
/** @var string $content main view render result */
?>
<?php $this->beginPage() ?>
<?php $this->beginBody() ?>
<?= $content ?>

--
<?= Yii::t('app', 'Kind Regards') ?>
<?= Yii::$app->params['company.name'] ?? '(Company Name)' ?>

<?php $this->endBody() ?>
<?php $this->endPage() ?>
