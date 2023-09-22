<?php

use app\components\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

use practically\chartjs\Chart;

use app\models\Application;
use app\assets\ChartJSAsset;

ChartJSAsset::register($this);

/** @var yii\web\View $this */

$this->title = Yii::t('app', 'Home') . ' :: ' . Yii::$app->name;

$this->context->fluid = false;
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4"><?= Yii::$app->name ?>!</h1>

        <p class="lead"><?= Yii::t('app', 'Manage costs in travels or other projects, and split them with your community.') ?></p>
        
        <?php if(Yii::$app->user->isGuest) : ?>
        <p>
            <?= Html::a(Html::icon('log-in') . Yii::t('app', 'Login'), ['user/login'], ['title' => Yii::t('app', 'Login as an existing user'), 'class' => 'btn btn-lg btn-primary']) ?>
            <?= Html::a(Html::icon('user-plus') . Yii::t('app', 'Register'), ['user/register'], ['title' => Yii::t('app', 'Regster as a new user'), 'class' => 'btn btn-lg btn-info']) ?>
        </p>
        <?php endif; ?>

    </div>

    <?php if(!Yii::$app->user->isGuest && $costprojects===0) : // No cost projects yet, show some help: ?>
        <?= \yii\bootstrap4\Alert::widget([
        'options' => [
            'class' => 'alert-info',
        ],
        'body' => Html::tag('h4', Yii::t('app', 'Getting Started'))
            . Yii::t('app', 'First create a new cost project.')
            . '<br>'
            . Yii::t('app', 'Then add expenses to the cost project.')
            . '<br>'
            . Html::tag('p', Html::a(Html::icon('plus-square') . Yii::t('app', 'Create the first cost project'), ['costproject/create'], ['class' => 'btn btn-primary btn-sm']), ['class' => 'mt-2'])
    ]) ?>
    <?php endif; ?>
    <div class="body-content">

        <div class="row"><!-- {{{ 1st row -->
            <div class="col-lg-6 card pl-3 pt-2">
                <h2><?= Yii::t('app', 'Cost Projects') ?></h2>
                <p><?= Yii::t('app', 'Manage projects.') ?></p>
                <p><?php echo Yii::t('app', 'Currently there {n,plural,=0{are no projects} =1{is <b>one</b> project} other{are <b>#</b> projects}} in this system.', ['n' => $costprojects ]); ?></p>
                <div class="btn-group mb-3" role="group" aria-label="Basic example">
                    <?= Html::a(Yii::t('app', 'List of Cost Projects').' '.'&raquo;', ['/costproject'], ['class'=>'btn btn-primary']) ?>
                    <?= Html::a(Html::icon('plus-square'), ['/costproject/create'], ['class'=>'btn btn-success', 'title' => Yii::t('app', 'Add new cost project')]) ?>
                </div>
            </div>
            <div class="col-lg-6 card pl-3 pt-2">
                <h2><?= Yii::t('app', 'Expenses') ?></h2>
                <p><?= Yii::t('app', 'Manage expenses.') ?></p>
                <p><?php echo Yii::t('app', 'Currently there {n,plural,=0{are no expenses} =1{is <b>one</b> expense} other{are <b>#</b> expenses}} in this system.', ['n' => $expenses ]); ?></p>
                <div class="btn-group mb-3" role="group" aria-label="Basic example">
                    <?= Html::a(Yii::t('app', 'List of Expenses').' '.'&raquo;', ['/expense'], ['class'=>'btn btn-primary']) ?>
                    <?= Html::a(Html::icon('plus-square'), ['/expense/create'], ['class'=>'btn btn-success', 'title' => Yii::t('app', 'Add new expense')]) ?>
                </div>
            </div>
        </div><!-- }}} end 1st row -->
        <div class="row mt-5"><!-- {{{ 2nd row -->
            <div class="col-lg-8 pr-2">
                <?= $this->render('pages/about') ?>
            </div>
            <div class="col-lg-4">
                 <h2><?= Yii::t('app', 'News') ?></h2>
                <?php if(!Yii::$app->user->isGuest) : ?>

                <?php 
                $newsSearchModel = new \app\modules\blog\models\search\PostSearch(); 
                if(!Yii::$app->user->can('blogAuthor')) { 
                    $newsSearchModel->status = \app\modules\blog\models\Post::STATUS_PUBLISHED; 
                }
                $newsDataProvider = $newsSearchModel->search(Yii::$app->request->queryParams);
                // TODO: $newsDataProvider->pagination->pageSize = Yii::$app->settings->get('AppParamsForm', 'homeNewsItemsPerPage');  
                $newsDataProvider->pagination->pageSize = 3;  
                ?>

                <div class="row" id="home-news-list-container">
                    <?= ListView::widget([ // {{{ News List
                        'id'=>'home-news-list',
                        'options' => [
                            'tag' => 'div',
                        ],
                        'dataProvider' => $newsDataProvider,
                        'itemView' => function ($model, $key, $index, $widget) {
                            $itemContent = $this->render('@app/modules/blog/views/post/_news_list_item',['model' => $model]);
                            return $itemContent;
             
                            /* Display an Advertisement after the first list item */
                            if ($index == 0) {
                                $adContent = $this->render('_news_list_add');
                                $itemContent = $adContent.$itemContent;
                            }
             
                            return $itemContent;
             
                            /* Or if you just want to display the list item only: */
                            // return $this->render('_list_item',['model' => $model]);
                        },
                        'itemOptions' => [
                            'tag' => false,
                        ],
                        'summary' => '',
                         
                        /* do not display {summary} */
                        'layout' => '{items}{pager}',
             
                        'pager' => [
                            'firstPageLabel' => 'First',
                            'lastPageLabel' => 'Last',
                            'maxButtonCount' => 4,
                            'options' => [
                                'class' => 'pagination col-xs-12'
                            ]
                        ],
             
                    ]); /* }}} End ListView */ ?>
                </div>

                <?php if(Yii::$app->user->can('blogAuthor')) : ?>
                <?= Html::a(Html::icon('plus-square'), ['/blog/post/create'], ['class' => 'btn btn-success btn-sm', 'title'=>Yii::t('app.blog','Create New Blog Post')]) ?>&nbsp;
                <?= Html::a(Html::icon('list'), ['/blog/post/index'], ['class' => 'btn btn-success btn-sm', 'title'=>Yii::t('app.blog','Manage Blog Posts')]) ?>
                <?php endif; ?>
        
                <?php else : ?>
                <p><?= Yii::t('app', 'Please login to see app news.') ?></p>
                <?php endif; ?>

                <?php if(!Yii::$app->user->isGuest) : ?>
                <div id="roles-info-box" style="margin-top: 15px">
                    <h3><?= Yii::t('app','Your Roles') ?></h3> 
                    <?php $roles = Yii::$app->authManager->getItemsByUser(Yii::$app->user->id); ?>
                    <?php $tmp=[]; foreach($roles as $role) : ?>
                    <?php $item = '('.$role->name.')'; if(!empty($role->description)) $item = $role->description .' ' . $item; $tmp[] = $item; endforeach; ?>
                    <?php echo join('; ', $tmp); ?>
                </div>
                <?php endif; ?>
            </div>
        </div><!-- }}} end 2nd row -->

    </div>
</div>
