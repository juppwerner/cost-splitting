<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

/**
 * @var \yii\web\View
 * @var yii\mail\BaseMessage $content
 */

$p_style = "font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;";
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
style="<?= $p_style ?>">
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <?php $this->head() ?>
</head>
<body bgcolor="#f6f6f6"
      style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; margin: 0; padding: 0;">
<table class="body-wrap"
       style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; width: 100%; margin: 0; padding: 20px;">
    <tr style="<?= $p_style ?>">
        <td style="<?= $p_style ?>"></td>
        <td class="container" bgcolor="#FFFFFF"
            style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto; padding: 0; border: 1px solid #f0f0f0;">
            <div class="content"
                 style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                <table style="<?= $p_style ?> width: 100%;">
                    <tr style="<?= $p_style ?>">
                        <td style="<?= $p_style ?>">
                            <?php $this->beginBody() ?>
                            <?= $content ?>
                            <p  style="<?= $p_style ?>"--<br>
                            <?= Yii::t('app', 'Kind Regards') ?><br>
                            <?= Yii::$app->params['company.name'] ?? '(Company Name)' ?></p>
                            <?php $this->endBody() ?>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td style="<?= $p_style ?>"></td>
    </tr>
</table>
<table class="footer-wrap" style="<?= $p_style ?> width: 100%; clear: both !important; ">
    <tr style="<?= $p_style ?>">
        <td style="<?= $p_style ?>"></td>
        <td class="container"
            style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0 auto; padding: 0; display: block !important; max-width: 600px !important; clear: both !important;">
            <div class="content"
                 style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                <table style="<?= $p_style ?> width: 100%;">
                    <tr style="<?= $p_style ?>">
                        <td align="center" style="<?= $p_style ?>">
                            <p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 1.6; color: #666; font-weight: normal; margin: 0 0 10px; padding: 0;">
                                © <?= Yii::$app->name ?> <?= date('Y') ?>.
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td style="<?= $p_style ?>"></td>
    </tr>
</table>
</body>
</html>
<?php $this->endPage() ?>
