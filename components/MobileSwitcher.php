<?php
/**
 * @author: Joachim Werner <joachim.werner@diggin-data.de> 
*/
 
namespace app\components;

use Yii;
use yii\base\BaseObject;

class MobileSwitcher extends BaseObject
{
    /**
     * Wether to show mobile views or not (i.e. show desktop views)
     * @var bool
     **/
    public $showMobile = false;

    /**
     * Name of Cookie variable which stores mobile or desktop views user decision
     * @var string
     */
    public $cookieVar = 'showMobile';

    public function __construct($config = [])
    {
        // ... initialization before configuration is applied

        parent::__construct($config);
    }

    public function init()
    {
        $logtag = 'app\components\MobileSwitcher'; 
        if(php_sapi_name() === 'cli')
        {
            return true;
        }
 
        parent::init();

        $cookiesRequest     = yii::$app->request->cookies;
        $cookiesResponse    = yii::$app->response->cookies;
        $showMobileNew      = yii::$app->request->get($this->cookieVar);
        
        if(!is_null($showMobileNew))        {
            Yii::debug('GET showMobileNew: '.$showMobileNew, $logtag);
            $this->showMobile = (bool)$showMobileNew;
            $cookiesResponse->add(new \yii\web\Cookie([
                'name' => $this->cookieVar,
                'value' => (bool)$showMobileNew,
                'sameSite' => PHP_VERSION_ID >= 70300 ? yii\web\Cookie::SAME_SITE_LAX : null,
            ]));
        }
        elseif($cookiesRequest->has($this->cookieVar))
        {
            Yii::debug('COOKIE '.$this->cookieVar.': '.$cookiesRequest->has($this->cookieVar), $logtag);
            $this->showMobile = (bool)$cookiesRequest->getValue($this->cookieVar);
        }
        else {
            Yii::debug('showMobile: Neither COOKIE nor GET set', $logtag);
            $this->showMobile = false;

        }
        Yii::debug('showMobile: '.\yii\helpers\VarDumper::dumpAsString($this->showMobile), $logtag);
    }
}