<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

/**
 * @var $this  yii\web\View
 * @var $model \Da\User\Model\Role
 */

use Da\User\Helper\AuthHelper;
// use dosamigos\selectize\SelectizeDropDownList;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$unassignedItems = Yii::$container->get(AuthHelper::class)->getUnassignedItems($model);
?>

<?php $form = ActiveForm::begin(
    [
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
    ]
) ?>

<?= $form->field($model, 'name') ?>

<?= $form->field($model, 'description') ?>

<?= '' /* $form->field($model, 'rule')->widget(SelectizeDropDownList::class, [
    'items' => ArrayHelper::map(Yii::$app->getAuthManager()->getRules(), 'name', 'name'),
    'options' => [
        'prompt' => 'Select rule...'
    ]
]) */ ?>
<?= $form->field($model, 'rule')->widget(Select2::classname(), [
    'data' => ArrayHelper::map(Yii::$app->getAuthManager()->getRules(), 'name', 'name'),
    'options' => ['placeholder' => Yii::t('app', 'Select rule ...')],
    'pluginOptions' => [
        // 'tags' => true,
        'tokenSeparators' => [',', ' '],
        'maximumInputLength' => 10
    ],
]) ?>

<?= '' /* $form->field($model, 'children')->widget(
    SelectizeDropDownList::class,
    [
        'items' => $unassignedItems,
        'options' => [
            'id' => 'children',
            'multiple' => true,
        ],
    ]
) */ ?>
<?= $form->field($model, 'children')->widget(Select2::classname(), [
    'data' => $unassignedItems,
    'options' => ['placeholder' => Yii::t('app', 'Select rule ...'), 'multiple' => true],
    'pluginOptions' => [
        'tags' => true,
        'tokenSeparators' => [',', ' '],
        'maximumInputLength' => 10
    ],
]) ?>

<?= Html::submitButton(Yii::t('usuario', 'Save'), ['class' => 'btn btn-success btn-block']) ?>

<?php ActiveForm::end() ?>
