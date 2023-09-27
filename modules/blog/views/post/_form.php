<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Post */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="post-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList( $model->statusOptions, ['prompt'=>Yii::t('app', '(Select...)')] ) ?>

    <?= $form->field($model, 'intro')->widget(\yii2mod\markdown\MarkdownEditor::class, [
        'editorOptions' => [
            'showIcons' => ["code", "table"],
            'blockStyles' => [
                'italic' => '_',
            ],
        ],
    ]);  ?>

    <?= $form->field($model, 'content')->widget(\yii2mod\markdown\MarkdownEditor::class, [
        'editorOptions' => [
            'showIcons' => ["code", "table"],
            'blockStyles' => [
                'italic' => '_',
            ],
            'insertTexts' => [
                'table' => ['',   "Item | Value\r\n"
                                . "---- | ----:\r\n"
                                . "One  | 123.4"
                ],
            ],
        ],
    ]);  ?>


    <div class="form-group">
        <label class="control-label col-sm-3"></label>
        <div class="col-sm-6">
            <?= Html::submitButton('<span class="fas fa-save"></span> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            <?= Html::submitButton('<span class="fas fa-redo"></span> ' . Yii::t('app', 'Reset'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
