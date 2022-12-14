<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Url;

use app\assets\AppAsset;
use app\widgets\Alert;

// Create copyright text
$copyrightReleaseYear = Yii::$app->params['applicationReleaseYear'];
$cTxt = $copyrightReleaseYear;
$thisYear = date('Y');
if($thisYear>$copyrightReleaseYear)
    $cTxt .= ' - '.$thisYear;

// Select default bootswatch theme, as per app paramms
$theme = Yii::$app->params['theme'];

$cookiesRequest     = Yii::$app->request->cookies;
$cookiesResponse    = Yii::$app->response->cookies;
$themeNew = Yii::$app->request->get('theme');
if($themeNew) {
    // {{{ Change theme
    if(raoul2000\bootswatch4\BootswatchAsset::isSupportedTheme($themeNew))
    // if(isset($this->languages[$languageNew]))
    {
        $theme = $themeNew;
        $cookiesResponse->add(new \yii\web\Cookie([
            'name' => 'theme',
            'value' => $themeNew
        ]));
    }
    // }}} 
} elseif($cookiesRequest->has('theme')) {
    $theme = $cookiesRequest->getValue('theme');
}

// Get array of themes
$availableThemeDirs = [ // {{{ 
    'cerulean',
    'cosmo',
    'cyborg',
    'darkly',
    'flatly',
    'journal',
    'lumen',
    'paper',
    'readable',
    'sandstone',
    'simplex',
    'slate',
    'spacelab',
    'superhero',
    'yeti',
];
ksort($availableThemeDirs);
// }}} 

$logoWhitePostfix = '';
// These themes are 'dark' and require a white company logo:
$themesLogoWhite = [ // {{{
    'cerulean', 
    'cosmo',
    'cyborg',
    'darkly',
    'flatly',
    'sandstone',
    'slate',
    'superhero',
    'yeti',
]; // }}} 
// Is current theme a 'dark' one, requiring a white logo?
if(in_array($theme, $themesLogoWhite))
    $logoWhitePostfix = '_weiss';

$availableThemeItems = array();
// print_r($availableThemeDirs);
foreach($availableThemeDirs as $themeDir)
{
    $temp = [];
    $temp['active'] = $theme == $themeDir ? true : false;
    $temp['label'] = ucfirst($themeDir);
    $temp['url'] = Url::current(['theme' => $themeDir]);
    $availableThemeItems[] = $temp;
}
raoul2000\bootswatch4\BootswatchAsset::$theme = $theme;
AppAsset::register($this);

// Check if app runs on localhost
$titlePrefix = '';
$breadcrumbsCssClass = ['breadcrumb'];
if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $titlePrefix = 'LOCALHOST ';
    $breadcrumbsCssClass = array_merge(['bg-light'], $breadcrumbsCssClass);
}
if(empty($this->title))
    $this->title = Yii::$app->name;
else
    $this->title .= ' :: '.Yii::$app->name;

// Common user roles
$isGuest = Yii::$app->user->isGuest;
$isAdmin = !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin;
$isDev = !Yii::$app->user->isGuest && strtolower(Yii::$app->user->identity->username)==='xzndzw';
// {{{ *** SETUP MENU ITEMS ***
$menuItems = [];
// {{{ HOME
// $menuItems[] = ['label' => '<i class="fas fa-home"></i> '.Yii::t('app', 'Home'), 'url' => ['/site/index']]; // }}} 
if ($isGuest) {
    // {{{ Login
    if( Yii::$app->getModule('user')->enableRegistration)
        $menuItems[] = ['label' => '<i class="fas fa-user-plus"></i> '.Yii::t('app', 'Register'), 'url' => ['/user/registration/register'], 'options'=>['id'=>'mnuRegister']]; 
    $menuItems[] = ['label' => '<i class="fas fa-sign-in-alt"></i> '.Yii::t('app', 'Login'), 'url' => ['/user/security/login'], 'options'=>['id'=>'mnuSignIn']]; // }}} 
} else {
    // {{{ APP PAGES
    $menuItems[] = ['label' => '<i class="fas fa-cog"></i> '    .Yii::t('app','App'), 'items' => [
        ['label' => '<i class="fas fa-home"></i> '              .Yii::t('app', 'Home'), 'url' => ['/site/index']],
        '<div class="dropdown-divider"></div>',
        ['label' => '<i class="fas fa-th-list"></i> '           .Yii::t('app','Cost Projects'), 'url' => ['/costproject/index']],
        ['label' => '<i class="fas fa-th-list"></i> '           .Yii::t('app','Expenses'), 'url' => ['/expense/index']],
        // '<div class="dropdown-divider"></div>',
        // ['label' => '<i class="fas fa-filter"></i> '            .Yii::t('app','List Filters'),    'url' => ['/listfilter/index']],
    ]]; // }}} 
    // {{{ SETTINGS
    $menuItems[] = ['label' => '<i class="fas fa-wrench"></i> '.Yii::t('app','Settings'), 'items' => [
        ['label' => '<i class="fas fa-user"></i> '.Yii::t('app','Profile Settings (User)'), 'url' => ['/user/settings'], 'visible'=>!$isGuest],
    ]]; // }}} 
    $idx = count($menuItems)-1;
    // {{{ THEMES
    // $menuItems[$idx]['items'][] = '<li class="divider"></li>';
    $menuItems[$idx]['items'][] = ['label' => '<i class="fas fa-tint"></i> '.\Yii::t('app','Themes ({theme})', ['theme'=>ucfirst($theme)]), 'items' => $availableThemeItems];
    // }}} 
    $menuItems[$idx]['items'][] = '<div class="dropdown-divider"></div>';
    if($isAdmin) {
        $oldItems = $menuItems[$idx]['items'];
        // {{{ ADMIN
        $newItems = [
            // '<li class="divider"></li>',
            // ['label'=>'<i class="fas fa-wrench"></i> '      .Yii::t('app','Site Settings'),     'url' => ['/site/manage-settings'], 'visible'=>!$isGuest && $isAdmin],
            ['label' => '<i class="fas fa-list"></i> '      .\Yii::t('app','Blog Admin'),       'url' => ['/blog/post/index'],      'visible'=>!$isGuest && $isAdmin],
            ['label' => '<i class="fas fa-user"></i> '      .\Yii::t('app','Users Admin'),      'url' => ['/user/admin', 'sort'=> '-last_login_at'], 'visible'=>!$isGuest && $isAdmin],
            ['label' => '<i class="fas fa-user"></i> '      .\Yii::t('app','Roles and Users'),  'url' => ['/user-management/roles-and-users']],
            ['label' => '<i class="fas fa-search"></i> '    .\Yii::t('app','Lookups'),          'url' => ['/lookup'],               'visible'=>$isAdmin],
        ]; // }}} 
        // {{{ DEVELOPER
        if($isDev) {
            // $newItems[] = '<li class="divider"></li>';
            $newItems[] = ['label' => '<i class="fas fa-cog"></i> '.'Gii', 'url' => ['/gii']];
        } // }}} 
        $menuItems[$idx]['items'] = array_merge($oldItems, $newItems);
        /* {{{ DEBUG
        \yii\helpers\VarDumper::dump($oldItems, 10, true);
        \yii\helpers\VarDumper::dump($newItems, 10, true);
        echo '<hr>';
        \yii\helpers\VarDumper::dump($menuItems, 10, true);
        die;
        }}} */
    }
// {{{ LANGUAGES
$menuItems[] = ['label' => '<i class="fas fa-language"></i> '.Yii::t('app', 'Language'), 'items' => [
    ['label' => Yii::t('app', 'English'), 'url' => Url::current(['language'=>'en'])],
    ['label' => Yii::t('app', 'German'), 'url' => Url::current(['language'=>'de'])],
]]; // }}} 
    // {{{ BLOG
    $menuItems[] = ['label' => '<i class="fas fa-newspaper"></i> '.Yii::t('app', 'Blog'), 'url' => ['/blog']]; // }}}
    // {{{ LOGOUT
    $menuItems[] = ['label' => '<i class="fas fa-sign-out-alt"></i> '.Yii::t('app', 'Logout ({username})', ['username'=>Yii::$app->user->identity->username]), 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']]; // }}}
    // {{{ SWITCH USER
    $userModule = Yii::$app->getModule('user');
    if(Yii::$app->session->has($userModule->switchIdentitySessionKey)) {
        $menuItems[] = '<li>' . Html::beginForm(['/user/admin/switch-identity'], 'post', ['class' => 'navbar-form'])
        . Html::submitButton('<span class="glyphicon glyphicon-user"></span> Back to original user',
            ['class' => 'btn btn-link']
        ) . Html::endForm() . '</li>';
    } // }}} 
}
// }}} End Menu Items
// \yii\helpers\VarDumper::dump($menuItems, 10, true); die;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="<?= Url::to('@web/favicon.ico', true) ?>" rel="shortcut icon" type="image/x-icon">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([ // {{{ 
        'brandLabel' => /* Html::img('@web/img/stell-logo-white.svg', ['style'=>'height:25px']) . '&nbsp;&nbsp;' . ' - ' . */
            Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        // Make nav bar extend to ful width:
        'innerContainerOptions'  => [
            'class' => 'container-fluid',
        ],
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);
    echo Nav::widget([
        'encodeLabels' => false,
        'options' => ['class' => 'navbar-nav'],
        'items' => $menuItems,
    ]);
    NavBar::end(); // }}} 
    ?>
</header>

<?php 
// controller/$fluid requests where to show fluid layout
$divClass='container';
if(isset($this->context->fluid) and $this->context->fluid==true)
    $divClass .= '-fluid';

// Breadcrums
$breadcrumbs = [['label' => Yii::t('yii', 'Home'), 'url' => Yii::$app->homeUrl]];
if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $breadcrumbs = array_merge(['DEV SERVER'], $breadcrumbs);
}
if(isset($this->params['breadcrumbs'])) {
    $breadcrumbs = array_merge($breadcrumbs, $this->params['breadcrumbs']);
}

?>

<main role="main" class="flex-shrink-0">
    <div class="<?= $divClass ?>">
        <?= Breadcrumbs::widget([
            'links' => $breadcrumbs,
            'homeLink' => false,
            'encodeLabels' => false,
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="<?= $divClass ?>">
        <p class="text-left"><b><?= Yii::$app->name ?></b> | 
            By <?= Html::a("Diggin' Data", 'https://www.diggin-data.de', ['target'=>'_blank']) ?> <?= $cTxt ?> | 
            <?= Html::a(Yii::t('app', 'Rev. {version}', ['version'=>Yii::$app->version]), ['/site/page', 'view'=>'changes']) ?> | 
            <?= Html::a(Yii::t('app', 'Contact'), ['/site/contact']) ?> |
            <?= Html::a(Yii::t('app', 'About'), ['/site/page', 'view'=>'about']) ?>
        </p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
