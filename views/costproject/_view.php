<?php
use app\components\Html;
use yii\widgets\DetailView;

use app\dictionaries\CurrencyCodesDictEwf;

/** @var yii\web\View $this */
/** @var app\models\Costproject $model */
?>

<?php if(!Yii::$app->mobileSwitcher->showMobile) : ?>

<?= DetailView::widget([
    'model' => $model,
    'options' => ['class' => 'table table-striped table-responsive-sm table-hover'],
    'attributes' => [
        [
            'attribute' => 'title',
            'format' => 'html',
            'value' => Html::tag('h4', $model->title),
            'visible' => false,
        ],
        [
            'attribute' => 'participants',
            'format' => 'html',
            'value' => nl2br($model->participants),
        ],
        [
            'attribute' => 'currency',
            'value' => CurrencyCodesDictEwf::get($model->currency),
            // 'visible' => $model->useCurrency,
        ],
        [
            'attribute' => 'useCurrency',
            'format' => 'checkbox',
        ],
        [
            'attribute' => 'description',
            'format' => 'html',
            'value' => function($model) {
                if(!empty($model->description)) {
                    return Html::tag(
                        'div',
                        Yii::$app->formatter->asMarkdown(Html::encode($model->description)),
                        ['style' => 'font-size: smaller']
                    );
                } else {
                    return null;
                }
            },
        ],
        [
            'label' => Yii::t('app', 'Users'),
            'format' => 'raw',
            'value' => function($data) {
                $tmp = [];
                foreach($data->users as $user) {
                    $item =  $user->displayName.' (#'.$user->id.')';
                    if((int)$user->id!==(int)Yii::$app->user->id && Yii::$app->user->can('updateCostproject', ['costproject'=>$data]))
                        $item .= ' ' . Html::a(Html::icon('trash-2'), ['remove-user', 'AddUserForm[costprojectId]'=>$data->id, 'AddUserForm[username]' => $user->username], [
                            'class' => 'btn btn-primary btn-sm',
                            'data' => [
                                'confirm' => Yii::t('app', 'Are you sure you want to remove this user?'),
                                'method' => 'post',
                            ]
                        ]);
                    $tmp[] = $item;
                }
                if($this->context->action->id!=='manage-users' && Yii::$app->user->can('updateCostproject', ['costproject'=>$data]))
                    $tmp[] = Html::a(Html::icon('plus-square') . Yii::t('app', 'Manage Users'), ['manage-users', 'id'=>$data->id], ['class' => 'btn btn-sm btn-primary mt-2']);
                return join('<br>', $tmp);
            }
        ],
        [
            'attribute' => 'orderId',
            'format' => 'raw',
            'value' => function($data) {
                if($data->isPaid)
                    return Html::tag('span', Yii::t('app', 'Paid'), ['class' => 'badge badge-success']) . ' ' . Yii::$app->formatter->asDatetime($data->ordered_at, 'short');
                else
                    return Html::tag('span', Yii::t('app', 'Not Paid'), ['class' => 'badge badge-success']);
            },
        ],
        // 'id',
    ],
]) ?>

<?php else : ?>

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['tag' => 'div', 'class' => 'list-group'],
        'template' => '<a class="list-group-item list-group-item-action"{contentOptions}><div class="d-flex w-100 justify-content-between"><h5>{label}</h4></div><p>{value}</p></a>',
        'attributes' => [
            [
                'attribute' => 'title',
                'format' => 'html',
                'value' => Html::tag('h4', $model->title),
                'visible' => false,
            ],
            [
                'attribute' => 'participants',
                'format' => 'html',
                'value' => nl2br($model->participants),
            ],
            [
                'attribute' => 'currency',
                'value' => CurrencyCodesDictEwf::get($model->currency),
                // 'visible' => $model->useCurrency,
            ],
            [
                'attribute' => 'useCurrency',
                'format' => 'checkbox',
            ],
            [ // description
                'attribute' => 'description',
                'format' => 'html',
                'value' => function($model) {
                    if(!empty($model->description)) {
                        return Html::tag(
                            'div',
                            Yii::$app->formatter->asMarkdown(Html::encode($model->description)),
                            ['style' => 'font-size: smaller']
                        );
                    } else {
                        return null;
                    }
                },
            ],
            [ // Users
                'label' => Yii::t('app', 'Users'),
                'format' => 'raw',
                'value' => function($data) {
                    $tmp = [];
                    foreach($data->users as $user) {
                        $item =  $user->displayName.' (#'.$user->id.')';
                        if((int)$user->id!==(int)Yii::$app->user->id && Yii::$app->user->can('updateCostproject', ['costproject'=>$data]))
                            $item .= ' ' . Html::a(Html::icon('trash-2'), ['remove-user', 'AddUserForm[costprojectId]'=>$data->id, 'AddUserForm[username]' => $user->username], [
                                'class' => 'btn btn-primary btn-sm',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to remove this user?'),
                                    'method' => 'post',
                                ]
                            ]);
                        $tmp[] = $item;
                    }
                    // return $item;
                    /* if($this->context->action->id!=='manage-users' && Yii::$app->user->can('updateCostproject', ['costproject'=>$data]))
                        $tmp[] = Html::a(Html::icon('plus-square') . Yii::t('app', 'Manage Users'), ['manage-users', 'id'=>$data->id], ['class' => 'btn btn-sm btn-primary mt-2']);
                    */
                    return join("\n", $tmp);
                }
            ],
            [
                'attribute' => 'orderId',
                'format' => 'raw',
                'value' => function($data) {
                    if($data->isPaid)
                        return Html::tag('span', Yii::t('app', 'Paid'), ['class' => 'badge badge-success']) . ' ' . Yii::$app->formatter->asDatetime($data->ordered_at, 'short');
                    else
                        return Html::tag('span', Yii::t('app', 'Not Paid'), ['class' => 'badge badge-success']);
                },
            ],
        ],
    ]) ?>
    <?php if($this->context->action->id!=='manage-users') : ?>
    <?= Html::a(Html::icon('plus-square') . Yii::t('app', 'Manage Users'), ['manage-users', 'id'=>$model->id], ['class' => 'btn btn-sm btn-primary mt-2']) ?>
    <?php endif; ?>
<?php endif; ?>
