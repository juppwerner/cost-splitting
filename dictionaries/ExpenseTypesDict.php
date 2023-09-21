<?php
namespace app\dictionaries;

use Yii;

abstract class ExpenseTypesDict
{
    const EXPENSETYPE_EXPENSE = 'expense';
    const EXPENSETYPE_TRANSFER = 'transfer';

    // {{{ all
    public static function all()
    {
        return [
            self::EXPENSETYPE_EXPENSE   => Yii::t('app', 'Expense'),
            self::EXPENSETYPE_TRANSFER   => Yii::t('app', 'Money Transfer'),
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

