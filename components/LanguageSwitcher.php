<?php
/*
author :: Pitt Phunsanit
website :: http://plusmagi.com
change language by get language=EN, language=TH,...
or select on this widget
*/
 
namespace app\components;
 
use Yii;
use yii\base\Component;
use yii\base\Widget;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Url;
use yii\web\Cookie;
 
class LanguageSwitcher extends Widget
{
    /**
     * Array of available language names, indexed by language code
     *
     * @var mixed
     */
    public $languages;
    // {{{ init
    /**
     * Initialise the application language.
     *
     * Either restore language from cookie or set new language from GET parameter.
     */
    public function init()
    {
        if(php_sapi_name() === 'cli')
        {
            return true;
        }
 
        parent::init();

        // Available translations/languages
        $this->languages = [
            'en' => Yii::t('app','English'),
            'de' => Yii::t('app','German'),
        ];
        
        $cookiesRequest     = Yii::$app->request->cookies;
        $cookiesResponse    = Yii::$app->response->cookies;
        $languageNew        = Yii::$app->request->get('language');
        // Was a new language submitted via GET parameter?
        if($languageNew)
        {
            // Is submitted new language within the array of available languages?
            if(isset($this->languages[$languageNew]))
            {
                Yii::$app->language = $languageNew;
                $cookiesResponse->add(new Cookie([
                    'name'      => 'language',
                    'value'     => $languageNew,
                    'expire'    => time() + 60*60*24*30, // 1 month after today
                ]));
            }
        }
        // Was language already selected and stored in a cookie?
        elseif($cookiesRequest->has('language'))
        {
            Yii::$app->language = $cookiesRequest->getValue('language');
        }
    } // }}} 
    // {{{ run
    /**
     * Dispay a dropdown button with all available languages
     */  
    public function run(){
        $languages = $this->languages;
        $current = '(not set)';
        if(array_key_exists(Yii::$app->language, $languages)) {
            $current = $languages[Yii::$app->language];
            unset($languages[Yii::$app->language]);
        }
 
        $items = [];
        foreach($languages as $code => $language)
        {
            $temp = [];
            $temp['label'] = $language;
            $temp['url'] = Url::current(['language' => $code]);
            array_push($items, $temp);
        }

        echo Yii::t('app', 'Language:').'&nbsp;'; 
        echo ButtonDropdown::widget([
            'label' => $current,
            'dropdown' => [
                'items' => $items,
            ],
        ]);
    } // }}} 
}
