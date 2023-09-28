<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Expense $model */

$this->title = Yii::t('app', 'Create Expense');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cost Projects'), 'url' => ['costproject/index']];
if(!empty($model->costprojectId)) {
    $costproject = \app\models\Costproject::findOne($model->costprojectId);
    if(!empty($costproject)) {
        $this->params['breadcrumbs'][] = ['label' => $costproject->recordName, 'url' => ['costproject/view', 'id' => $model->costprojectId]];
    }
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model'         => $model,
        'costprojects'  => $costprojects,
        'participants'  => $participants,
        'titles'        => $titles,
    ]) ?>

</div>

<?php if(!empty($model->costprojectId)) : ?>
<?php $this->registerJs("
    $('#expense-title').focus();
",
    yii\web\View::POS_READY,
    'focus-title'
); ?>

<?php endif; ?>
