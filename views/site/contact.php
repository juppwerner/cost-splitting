<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var app\models\ContactForm $model */

use app\components\Html;

use yii\bootstrap4\ActiveForm;
use yii\captcha\Captcha;

$this->title = Yii::t('contact', 'Contact');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success alert-dismissible fade show ">
            <h4><?= Yii::t('contact', 'Thank you for contacting us.') ?></h4>
            <?= Yii::t('contact', 'We will respond to you as soon as possible.') ?> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <p><?= Html::a(Html::icon('arrow-left').Yii::t('contact', 'Return to home page'), ['/site/index'], ['class' => 'btn btn-primary']) ?></p>

        <p>
            Note that if you turn on the Yii debugger, you should be able to view the mail message on the mail panel of the debugger.
            <?php if (Yii::$app->mailer->useFileTransport): ?>
                Because the application is in development mode, the email is not sent but saved as
                a file under <code><?= Yii::getAlias(Yii::$app->mailer->fileTransportPath) ?></code>.
                Please configure the <code>useFileTransport</code> property of the <code>mail</code>
                application component to be false to enable email sending.<br>
                <br>
                This file: <code>@views/site/contact</code>
            <?php endif; ?>
        </p>

    <?php else: ?>

        <p>
            <?= Yii::t('contact', 'If you have business inquiries or other questions, please fill out the following form to contact us.') ?>
            <?= Yii::t('contact', 'Thank you.') ?>
        </p>

        <div class="row">
            <div class="col-lg-5">

                <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                    <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'email') ?>

                    <?= $form->field($model, 'subject') ?>

                    <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                        'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                    ])->hint(Yii::t('contact', 'Click on the code to get another one')) ?>

                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('contact', 'Submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>

    <?php endif; ?>
</div>
