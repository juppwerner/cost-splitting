<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $dataProvider array
 * @var $searchModel  \Da\User\Search\RoleSearch
 * @var $this         yii\web\View
 */

$this->title = Yii::t('usuario', 'Roles');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent('@Da/User/resources/views/shared/admin_layout.php') ?>
<div class="table-responsive-sm">
<?= GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'name',
                'header' => Yii::t('usuario', 'Name'),
                'options' => [
                    'style' => 'width: 20%',
                ],
            ],
            [
                'attribute' => 'description',
                'header' => Yii::t('usuario', 'Description'),
                'options' => [
                    'style' => 'width: 55%',
                ],
            ],
            [
                'attribute' => 'rule_name',
                'header' => Yii::t('usuario', 'Rule name'),
                'options' => [
                    'style' => 'width: 20%',
                ],
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model) {
                    return Url::to(['/user/role/' . $action, 'name' => $model['name']]);
                },
                'options' => [
                    'style' => 'width: 5%',
                ],
            ],
        ],
    ]
) ?>
</div>
<?php $this->registerJs("
$('body > main > div.container').removeClass('container').addClass('container-fluid');
", View::POS_READY, 'add-class' ); ?>
<?php $this->endContent() ?>
