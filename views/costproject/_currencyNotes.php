<div class="mt-2 mb-2 text-right" style="font-size:smaller">
    <?php
    use yii\bootstrap4\Modal;
    Modal::begin([
        'title' => Yii::t('app', 'About Currencies'),
        'toggleButton' => [
            'label' => \app\components\Html::icon('help-circle') . Yii::t('app', 'Currencies'), 
            'class' => 'btn btn-sm btn-secondary'
        ],
        'closeButton' => [
            'label' => \app\components\Html::icon('x-square'), 
            'class' => 'btn btn-secondary'
        ],
    ]);
    ?>
        <div class="text-left">
        <p><?= Yii::t('app', '<b>Currency</b> is used to calculate the total project cost, and also the participants costs.') ?></p>
        <p><?= Yii::t('app', '<b>Use Currency</b> is used to calculate the single expense costs,<br>noted in a foreign currency, into the project currency.') ?></p>
        </div>
    <?php Modal::end(); ?>
</div>