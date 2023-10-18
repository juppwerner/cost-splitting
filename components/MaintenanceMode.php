<?php
namespace app\components;
/**
 * Maintenance mode for Yii framework.
 * @author Karagodin Evgeniy (ekaragodin@gmail.com)
 * v 1.0
 */
use Yii;
use yii\base\BaseObject;
use yii\base\BootstrapInterface;
use yii\web\Application;

class MaintenanceMode extends BaseObject implements BootstrapInterface
{

    public $enableMode;
    public $captiveUrl = 'maintenance/index';
    public static $message = "This website is currently in maintenance mode.";

    public $alertClass='info';
    public $users = array('admin',);
    public $roles = array('Administrator',);

    public $ips = array();//allowed IP
    
    public $urls = array();

    public function __construct($config = [])
    {
        // ... initialization before configuration is applied
        foreach($config as $key=>$value) {
            if(in_array($key, ['enableMode', 'message', 'alertClass']))
                $this->$key = $value;
        }
        // Do thsi last:
        parent::__construct($config);
    }

    /**
     * @param \yii\web\Application $app
     * @throws \yii\base\InvalidConfigException
     */
    public function bootstrap($app)
    {
        parent::init();

        if(array_key_exists('maintenance.enabled', $app->params))
            $this->enableMode = (bool)$app->params['maintenance.enabled'];

        if (!$this->enableMode) 
            return;

        $disable = false;
        if(!$app->user->isGuest) {
            $disable = in_array(Yii::$app->user->identity->username, $this->users);
            foreach ($this->roles as $role) {
                $disable = $disable || Yii::$app->user->checkAccess($role);
            }
        }
        $disable = $disable || in_array(Yii::$app->request->getPathInfo(), $this->urls);
        
        $disable = $disable || in_array($this->getIp(), $this->ips);//check "allowed IP"

        if (!$disable) {
            // if ($this->captiveUrl === 'maintenance/index') {
            //     Yii::$app->controllerMap['maintenance'] = 'app\controllers\MaintenanceController';
            // }

            $app->catchAll = array($this->captiveUrl);
        }

    }

    //get user IP
    protected function getIp()
    {
        $strRemoteIP = $_SERVER['REMOTE_ADDR'];
        if (!$strRemoteIP) { $strRemoteIP = urldecode(getenv('HTTP_CLIENTIP')); }
        if (getenv('HTTP_X_FORWARDED_FOR')) { $strIP = getenv('HTTP_X_FORWARDED_FOR'); }
        elseif (getenv('HTTP_X_FORWARDED')) { $strIP = getenv('HTTP_X_FORWARDED'); }
        elseif (getenv('HTTP_FORWARDED_FOR')) { $strIP = getenv('HTTP_FORWARDED_FOR'); }
        elseif (getenv('HTTP_FORWARDED')) { $strIP = getenv('HTTP_FORWARDED'); }
        else { $strIP = $_SERVER['REMOTE_ADDR']; }

        if ($strRemoteIP != $strIP) { $strIP = $strRemoteIP.", ".$strIP; }
        return $strIP;
    }

    public static function getMessage()
    {
        return self::$message;
    }
    public static function getAlertClass()
    {
        return self::$alertClass;
    }
}