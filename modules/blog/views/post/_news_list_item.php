<?php
// Script to display a single list item
use yii\helpers\Html;

use app\modules\blog\models\Post;

$labels = [
    Post::STATUS_DRAFT      => ['class'=>'warning', 'label' => Yii::t('app','Draft')],
    Post::STATUS_PUBLISHED  => ['class'=>'success', 'label' => Yii::t('app','Published')],
    Post::STATUS_ARCHIVED   => ['class'=>'default', 'label' => Yii::t('app','Archived')],
];
// TODO: $homeNewsItemWordLimit = Yii::$app->settings->get('AppParamsForm', 'homeNewsItemWordLimit');
$homeNewsItemWordLimit = 10;
?>
 
<article class="list-item col-sm-12" data-key="<?= $model['id'] ?>">
    <strong><?= Yii::$app->formatter->asDateTime($model['updated_at'], 'short') ?></strong><br />
    <p><?= Html::encode($model['title']); ?> - <?= Yii::$app->myUtils->limitWords($model['intro'], $homeNewsItemWordLimit) ?><br>
        <?= Html::a('<span class="glyphicon glyphicon-chevron-right"></span> '.Yii::t('app','More...'), ['/blog/post/view', 'id'=>$model['id']], ['title'=>Yii::t('app', 'Read more on this topic: {title}', ['title'=>$model['title']])]) ?>
        <?php if(Yii::$app->user->can('author')) : ?>
        <span class="label label-<?= $labels[$model['status']]['class'] ?>"><?= $labels[$model['status']]['label'] ?></span>
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['post/update', 'id'=>$model['id']], ['class'=>'btn btn-success btn-xs']) ?>
        <?php endif; ?>
    </p>
    
</article>
