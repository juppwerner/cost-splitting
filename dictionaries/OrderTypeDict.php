<?php
namespace app\dictionaries;

use Yii;

abstract class OrderTypeDict
{
    const TIME = 'time';
    const QUANTITY = 'quantity';

    // {{{ all
    public static function all()
    {
        return [
            self::TIME   => Yii::t('app', 'Time-based'),
            self::QUANTITY   => Yii::t('app', 'Quantity-based'),
        ];
    } // }}} 
    // {{{ get
    public static function get($key)
    {
        $all = self::all();

        if (isset($all[$key])) {
            return $all[$key];
        }

        return Yii::t('app', '(not set)');
    } // }}} 
}
