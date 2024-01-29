<?php
namespace app\components;

use yii\helpers\Markdown;
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

    public function asMarkdown($value)
    {
        return Markdown::process($value, 'extra');
    }

    public function asJson($value, $tag=null)
    {
        $result = json_encode(json_decode($value), JSON_PRETTY_PRINT);
        if(!is_null($tag))
            return \yii\helpers\Html::tag($tag, $result);
        return $result;
    }
}
