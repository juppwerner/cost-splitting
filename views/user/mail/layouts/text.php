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
 * @var string main view render result
 */
?>

<?php $this->beginPage() ?>
<?php $this->beginBody() ?>
<?= $content ?>

--<?= PHP_EOL ?>
<?= Yii::t('app', 'Kind Regards') ?><?= PHP_EOL ?>
<?= Yii::$app->params['company.name'] ?? '(Company Name)' ?>
<?php $this->endBody() ?>
<?php $this->endPage() ?>
