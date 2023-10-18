<?php

$params = require __DIR__ . '/params.php';
// Load additional Parameters?
if(file_exists(__DIR__ . '/params-local.php')) {
    $params = array_merge(
        require(__DIR__ . '/params.php'),
        require(__DIR__ . '/params-local.php')
    );
}

$db = require __DIR__ . '/db.php';
// Load additinal DB settings?
if(file_exists(__DIR__ . '/db-local.php')) {
    $db     = array_merge(
        require(__DIR__ . '/db.php'),
        require(__DIR__ . '/db-local.php')
    );
}

$config = [
    'id' => 'cost-splitting',
    'name' => Yii::t('app', 'Cost Splitting'),
    'basePath' => dirname(__DIR__),
    'params' => $params,
    // The time zone used by this application.
    'timeZone'=>'Europe/Berlin',
     // set target language to be German
    'language' => 'de-DE',
    // set source language to be English
    'sourceLanguage' => 'en-US',
    'bootstrap' => [ // {{{ 
        'log',
        'app\components\MaintenanceMode',
        'languageSwitcher',
    ], // }}} 
    'aliases' => [ // {{{ 
        '@bower'    => '@vendor/bower-asset',
        '@npm'      => '@vendor/npm-asset',
        '@data'     => realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data'),
    ], // }}} 
    'components' => [ // {{{ 
        'authManager' => [ // {{{ 
            'class' => 'Da\User\Component\AuthDbManagerComponent',
        ], // }}} 
        'cache' => [ // {{{ 
            'class' => 'yii\caching\FileCache',
        ], // }}} 
        'db' => $db,
        'errorHandler' => [ // {{{ 
            'errorAction' => 'site/error',
        ], // }}} 
        'formatter' => [ // {{{ 
            // Use own formatter for e.g. asCheckbox
            'class' => '\app\components\Formatter',
            //'timeZone' => !empty(\Yii::$app->user) && \Yii::$app->user->isGuest ? 'UTC' : \Yii::$app->user->identity->profile->timezone,
        ], // }}} 
        'i18n' => [ // {{{ 
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'contact*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'contact' => 'contact.php'
                    ],
                ],
                'currcodes*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'currcodes' => 'currcodes.php'
                    ],
                ],
                'exchangerate*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'lookup' => 'exchangerate.php',
                    ],
                ],
                'lookup*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'lookup' => 'lookup.php',
                    ],
                ],
                'user' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@Da/User/resources/i18n',
                ],
                /*
                'yii2mod.comments' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2mod/comments/messages',
                ],
                'yii2mod.settings' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2mod/settings/messages',
                ],
                 */
            ],
        ], // }}}
        'languageSwitcher' => [ // {{{ 
            'class' => 'app\components\LanguageSwitcher',
        ], // }}} 
        'log' => [ // {{{ 
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ], // }}} 
        'mailer' => [ // {{{ 
            'class' => 'yii\symfonymailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ], // }}} 
        'myUtils' => [ // {{{ 
            'class' => 'app\components\MyUtils',
        ], // }}} 
        'request' => [ // {{{ 
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'y8350FxbcGgREnAVHTDhyYPTD2YdoqR8',
            'csrfParam' => '_csrf-MYTINYTODOS',
            'parsers' => [
                'application/json' => \yii\web\JsonParser::class
            ],
        ], // }}} 
        'session' => [ // {{{ 
            'name' => 'sess-pfea',
        ], // }}} 
        'urlManager' => [ // {{{ 
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ], // }}} 
        /* 'user' => [ // {{{ 
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ], // }}} */
        'view' => [ // {{{ 
            'theme' => [
                'pathMap' => [
                    '@Da/User/resources/views' => '@app/views/user',
                ],
            ],
        ], // }}} 
    ], // }}} 
    'modules' => [ // {{{ 
        'api' => [ // {{{ 
            'class' => 'app\modules\api\Module',
        ], // }}} 
        'blog' => [ // {{{ 
            'class' => 'app\modules\blog\Module',
        ], // }}}
        'exchangerate' => [
            'class' => 'app\modules\exchangerate\Module',
        ],
        'files' => [
            'class' => 'floor12\files\Module',
            'storage' => '@app/data/storage',
            'cache' => '@app/data/storage_cache',
            'token_salt' => '232j3i3be3uh439ueh39dh39uhe3u9h',
        ],
        'gridview' =>  [ // {{{ 
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to  
            // use your own export download action or custom translation 
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ], // }}} 
        'lookup' => [ // {{{
            'class' => 'app\modules\lookup\Module',
        ], // }}}
        'user' => [ // {{{ 
            // 'class' => Da\User\Module::class,
            'class' => app\components\UserModule::class,
            // ...other configs from here: [Configuration Options](installation/configuration-options.md), e.g.
            'administratorPermissionName' => 'admin',   // this is required role for accessing administrative actions
            'enableRegistration' => false,              // Don't allow user self registration, we will create admin users manually
            'enableTwoFactorAuthentication' => true,
            // 'generatePasswords' => true,
            // 'switchIdentitySessionKey' => 'myown_usuario_admin_user_key',

            'controllerMap' => [
                'registration' => [
                    'class' => \Da\User\Controller\RegistrationController::class,
                    'on ' . \Da\User\Event\FormEvent::EVENT_AFTER_REGISTER => function (\Da\User\Event\FormEvent $event) {
                        \Yii::$app->controller->redirect(['/site/index']);
                        \Yii::$app->end();
                    },
                    'on ' . \Da\User\Event\UserEvent::EVENT_AFTER_CONFIRMATION => function (\Da\User\Event\UserEvent $event) {
                        // Assign author role to user
                        $user = $event->getUser();
                        $auth = Yii::$app->authManager;
                        $author = $auth->getRole('author');
                        $auth->assign($author, $user->id);
                        \Yii::$app->controller->redirect(['/site/index']);
                        \Yii::$app->end();
                    },
                ],
            ],
            'classMap' => [
                'User' => app\models\User::class,
                'Profile' => app\models\Profile::class,
                'RegistrationForm' => app\models\forms\RegistrationForm::class,
                'RecoveryForm' => 'app\models\forms\RecoveryForm',            
            ],
            /* 'mailer' => [
                 'viewPath' => '@app/views/user/mail',
            ], */
        ], // }}} 
    ], // }}} 
    'container' => [
        'singletons' => [
            'app\components\MaintenanceMode' => [
                'class' => 'app\components\MaintenanceMode',
                'enableMode' => function() use ($params) { 
                    $enabled = false;
                    if(array_key_exists('maintenance.enabled', $params))
                        $enabled = (bool) $params['maintenance.enabled'];
                    return $enabled; 
                },
                'urls' => [
                    'debug/default/toolbar',
                    'debug/default/view',
                ],
                'alertClass' => 'warning',
            ],
        ],
    ],
];

if (YII_ENV_DEV) { // {{{ 
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
} // }}} 

return $config;
