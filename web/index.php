<?php
use codemix\yii2confload\Config;

// comment out the following two lines when deployed to production
// This is done via the .env file now!
// defined('YII_DEBUG') or define('YII_DEBUG', true);
// defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
$config = new Config(__DIR__ . '/..', true);
// DEBUG die(\yii\helpers\VarDumper::dumpAsString($config->web(), 10, true));

require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// Read applicatipon version
$version = '0.0.1';
$versionFile = dirname(__FILE__).'/../VERSION';
if(file_exists($versionFile)) {
    $version = file_get_contents($versionFile);
}

$config_web = $config->web();
// DEBUG \yii\helpers\VarDumper::dump($config_web, 10, true);
// DEBUG die;

$app = new yii\web\Application($config_web);
$app->name = Yii::t('app', '_appName_');

// Merge mail parameters
$mailParams = [
    'welcomeMailSubject'        => Yii::t('app', 'Welcome to {0}', $app->name),
    'confirmationMailSubject'   => Yii::t('app', 'Confirm account on {0}', $app->name),
    'reconfirmationMailSubject' => Yii::t('app', 'Confirm email change on {0}', $app->name),
    'recoveryMailSubject'       => Yii::t('app', 'Complete password reset on {0}', $app->name),
    'twoFactorMailSubject'      => Yii::t('app', 'Code for two factor authentication on {0}', $app->name),
];
if(isset($config_web['modules']['user']['mailParams'])) {
    $app->getModule('user')->mailParams = \yii\helpers\ArrayHelper::merge(
        $config_web['modules']['user']['mailParams'],
        $mailParams
    );
} else {
    $app->getModule('user')->mailParams = $mailParams;
}

$app->setVersion($version);
$app->run();