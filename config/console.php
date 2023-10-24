<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';



$params = require __DIR__ . '/params.php';
// Additional Parameters:
if(file_exists(__DIR__ . '/local-params.php')) {
    $params = yii\helpers\ArrayHelper::merge(
        require(__DIR__ . '/params.php'),
        require(__DIR__ . '/local-params.php')
    );
}
/* @var codemix\yii2confload\Config $this */
$web = $this->web();
$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [ // {{{ 
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ], // }}}
    'components' => [ // {{{ 
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $web['components']['db'],
        'mailer' => [
            'class' => 'yii\symfony\Mailer',
            'useFileTransport' => true,
        ],
    ], // }}} 
    'controllerMap' => [ // {{{ 
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
        'migrate' => [ // {{{ 
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationPath' => [
                '@app/migrations',
                '@yii/rbac/migrations', // Just in case you forgot to run it on console (see next note)
                '@app/modules/lookup/migrations',
                '@vendor/floor12/yii2-module-files/src/migrations'
            ],
            'migrationNamespaces' => [
                'Da\User\Migration',
            ],
        ], // }}}
        'migration' => [
            'class' => 'bizley\migration\controllers\MigrationController',
        ],
    ], // }}}
    'modules' => [ // {{{ 
        'user' => [ // {{{ 
            'class' => Da\User\Module::class,
            // ...other configs from here: [Configuration Options](installation/configuration-options.md), e.g.
            // 'administrators' => ['admin'], // this is required for accessing administrative actions
            // 'generatePasswords' => true,
            // 'switchIdentitySessionKey' => 'myown_usuario_admin_user_key',
        ], // }}}
    ], // }}}
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
