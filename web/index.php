<?php
if(file_exists(__DIR__.'/../env.php'))
    require(__DIR__ . '/../env.php');

// comment out the following two lines when deployed to production
if($_SERVER['SERVER_NAME']=='localhost')
    defined('YII_DEBUG') or define('YII_DEBUG', true);
else
    defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// Get application config:
$config = require(__DIR__ . '/../config/web.php');
// Load additional local config settings?
if(file_exists(__DIR__ . '/../config/web-local.php')) {
    $config = \yii\helpers\ArrayHelper::merge(
        $config,
        require(__DIR__ . '/../config/web-local.php')
    );
}
// DEBUG die(\yii\helpers\VarDumper::dumpAsString($config, 10, true));


$version = '0.0.1';
$versionFile = dirname(__FILE__).'/../VERSION';
if(file_exists($versionFile)) {
    $version = file_get_contents($versionFile);
}
$app = new yii\web\Application($config);

// Merge mail parameters
$mailParams = [
    'welcomeMailSubject'        => Yii::t('app', 'Welcome to {0}', $app->name),
    'confirmationMailSubject'   => Yii::t('app', 'Confirm account on {0}', $app->name),
    'reconfirmationMailSubject' => Yii::t('app', 'Confirm email change on {0}', $app->name),
    'recoveryMailSubject'       => Yii::t('app', 'Complete password reset on {0}', $app->name),
    'twoFactorMailSubject'      => Yii::t('app', 'Code for two factor authentication on {0}', $app->name),
];
if(isset($config['modules']['user']['mailParams'])) {
    $app->getModule('user')->mailParams = \yii\helpers\ArrayHelper::merge(
        $config['modules']['user']['mailParams'],
        $mailParams
    );
} else {
    $app->getModule('user')->mailParams = $mailParams;
}

$app->setVersion($version);
$app->run();

