<?php

namespace app\modules\blog;

/**
 * blog module definition class
 */
class Module extends \yii\base\Module
{
    public $defaultRoute = 'post/index';
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\blog\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
