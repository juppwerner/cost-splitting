<?php
namespace app\dictionaries;

use Yii;

abstract class ExampleDict
{
    const MYCONST1 = 'Constant1';
    const MYCONST2 = 'Constant2';
    const MYCONST3 = 'Constant3';

    // {{{ all
    public static function all()
    {
        return [
            self::MYCONST   => Yii::t('app', 'My Constant 1'),
            self::MYCONST   => Yii::t('app', 'My Constant 2'),
            self::MYCONST3  => Yii::t('app', 'My Constant 3'),
        ];
    } // }}} 
    // {{{ get
    public static function get($key)
    {
        $all = self::all();

        if (isset($all[$key])) {
            return $all[$key];
        }

        return Yii::t('app', '(not set');
    } // }}} 
}
