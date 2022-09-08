<?php
/**
 * BaseController used to extend other controllers
 * (similar to protected/components/Controller.php in Yii 1.x)
 */

namespace app\components;

use yii\web\Controller;

class BaseController extends Controller
{

    public $leftMenu = null;
    public $fluid = true;

}
