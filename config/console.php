<?php

$params = require __DIR__ . '/params.php';
// Additional Parameters:
if(file_exists(__DIR__ . '/params-local.php')) {
    $params = array_merge(
        require(__DIR__ . '/params.php'),
        require(__DIR__ . '/params-local.php')
    );
}

$db = require __DIR__ . '/db.php';
if(file_exists(__DIR__ . '/db-local.php')) {
    $db     = array_merge(
        require(__DIR__ . '/db.php'),
        require(__DIR__ . '/db-local.php')
    );
}

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
        'db' => $db,
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
    ], // }}} 
    'params' => $params,
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
            ],
            'migrationNamespaces' => [
                'Da\User\Migration',
                'nemmo\attachments\migrations',
            ],
        ], // }}}
    ], // }}}
    'modules' => [ // {{{ 
        'attachments' => [ // {{{ 
            'class' => nemmo\attachments\Module::className(),
            'tempPath' => '@app/data/uploads/temp',
            'storePath' => '@app/data/uploads/store',
            'rules' => [ // Rules according to the FileValidator
                'maxFiles' => 10, // Allow to upload maximum 3 files, default to 3
                // 'mimeTypes' => 'image/png', // Only png images
                'maxSize' => 5 * 1024 * 1024 // 1 MB
            ],
            'tableName' => '{{%attachments}}' // Optional, default to 'attach_file'
        ], // }}}
        'user' => [ // {{{ 
            'class' => Da\User\Module::class,
            // ...other configs from here: [Configuration Options](installation/configuration-options.md), e.g.
            // 'administrators' => ['admin'], // this is required for accessing administrative actions
            // 'generatePasswords' => true,
            // 'switchIdentitySessionKey' => 'myown_usuario_admin_user_key',
        ], // }}}
    ], // }}}
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
