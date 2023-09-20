<?php
use app\components\Html;
use yii\widgets\DetailView;

use app\dictionaries\CurrencyCodesDict;

/** @var yii\web\View $this */
/** @var app\models\Costproject $model */
?>
<?= DetailView::widget([
    'model' => $model,
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
            'value' => CurrencyCodesDict::get($model->currency),
            'visible' => $model->useCurrency,
        ],
        [
            'attribute' => 'useCurrency',
            'format' => 'checkbox',
        ],
        // 'id',
    ],
]) ?>
