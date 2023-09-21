<?php
namespace app\components;

use app\components\Html;

class Formatter extends \yii\i18n\Formatter
{
    public function asCheckbox($value)
    {
        // translate your int value to something else...
        switch ((bool)$value) {
            case true:
                return Html::icon('check-square');
            case false:
                return Html::icon('square');
            default:
                return $value;
        }
    }
}