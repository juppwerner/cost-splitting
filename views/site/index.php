<?php

use yii\bootstrap4\Html;
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

        <p class="lead"><?= Yii::t('app', 'Manage costs and split them with your community.') ?></p>

    </div>

    <div class="body-content">

        <div class="row"><!-- {{{ 1st row -->
            <div class="col-lg-6 card pl-2 pt-2">
                <h2><?= Yii::t('app', 'Cost Projects') ?></h2>
                <p><?= Yii::t('app', 'Manage projects.') ?></p>
                <p><?php echo Yii::t('app', 'Currently there {n,plural,=0{are no projects} =1{is <b>one</b> project} other{are <b>#</b> projects}} in this system.', ['n' => \app\models\Costproject::find()->count()  ]); ?></p>
                <p><?= Html::a( '<i class="fas fa-th-list"></i> '.Yii::t('app', 'List of Cost Projects').' '.'&raquo;', ['/costproject'], ['class'=>'btn btn-primary']) ?></p>
            </div>
            <div class="col-lg-6 card pl-2 pt-2">
                <h2><?= Yii::t('app', 'Expenses') ?></h2>
                <p><?= Yii::t('app', 'Manage expenses.') ?></p>
                <p><?php echo Yii::t('app', 'Currently there {n,plural,=0{are no expenses} =1{is <b>one</b> expense} other{are <b>#</b> expenses}} in this system.', ['n' => \app\models\Expense::find()->count()  ]); ?></p>
                <p><?= Html::a( '<i class="fas fa-th-list"></i> '.Yii::t('app', 'List of Expenses') , ['/expense'], ['class'=>'btn btn-primary']) ?></p>
            </div>
        </div><!-- }}} end 1st row -->
        <div class="row"><!-- {{{ 2nd row -->
            <div class="col-lg-8">
                <h2>Heading</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>
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
                <?= Html::a('<span class="fas fa-plus"></span>', ['/blog/post/create'], ['class' => 'btn btn-success btn-sm', 'title'=>Yii::t('app.blog','Create New Blog Post')]) ?>&nbsp;
                <?= Html::a('<span class="fas fa-th-list"></span>', ['/blog/post/index'], ['class' => 'btn btn-success btn-sm', 'title'=>Yii::t('app.blog','Manage Blog Posts')]) ?>
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
