<?php
/*
 * This file is part of the CertManager project.
 *
 */

namespace app\components;

use Da\User\Helper\TimezoneHelper as BaseTimezoneHelper;

class TimezoneHelper extends BaseTimezoneHelper
{
    // {{{ getAllByContinentAndTown
    /**
     * Get all of the time zones with the offsets sorted by their continent as first index, then by town names ascending.
     *
     * @throws InvalidParamException
     * @return array
     *
     */
    public static function getAllByContinentAndTown()
    {
        $timezones = self::getAll();
        $timezoneOptions = array();
        // \yii\helpers\VarDumper::dump($timezones, 10, true);
        foreach($timezones as $timezone) {
            if($timezone['timezone']==='UTC') {
                $timezone['timezone'] = 'Europe/UTC';
                $timezone['name'] = 'UTC (+0) (Europe)';
            }
            list($continent, $town) = explode('/', $timezone['timezone']);
            if(!array_key_exists($continent, $timezoneOptions))
                $timezoneOptions[$continent] = array();
            $timezoneOptions[$continent][$timezone['timezone']] = str_replace($continent.'/', ' ', $timezone['name']).' ('.$continent.')';
        }
        foreach($timezoneOptions as $continent=>$continentTimezoneOptions) {
            asort($continentTimezoneOptions);
            $timezoneOptions[$continent] = $continentTimezoneOptions;
        }
        ksort($timezoneOptions);
        return $timezoneOptions;
    } // }}} 
}
