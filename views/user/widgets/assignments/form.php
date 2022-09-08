<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use dosamigos\selectize\SelectizeDropDownList;
use yii\bootstrap4\Alert;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/**
 * @var $this           yii\web\View
 * @var $model          Da\User\Model\Assignment
 * @var $availableItems string[]
 */

?>

<?php if ($model->updated): ?>

    <?= Alert::widget(
        [
            'options' => [
                'class' => 'alert-success',
            ],
            'body' => Yii::t('usuario', 'Assignments have been updated'),
        ]
    ) ?>

<?php endif ?>

<?php $form = ActiveForm::begin(
    [
        'layout' => 'horizontal',
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-9',
            ],
        ],
    ]
) ?>

<?= Html::activeHiddenInput($model, 'user_id') ?>

<?= $form->field($model, 'items')->widget(
    SelectizeDropDownList::class,
    [
        'items' => $availableItems,
        'options' => [
            'id' => 'children',
            'multiple' => true,
        ],
    ]
) ?>

<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?= Html::submitButton(Yii::t('usuario', 'Update assignments'), ['class' => 'btn btn-success btn-block']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
