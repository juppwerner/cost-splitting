<?php
/*
author :: Joachim Werner <joachim.pt.werner@de.opel.com> 
Sonme utilities
*/
 
namespace app\components;

use yii\base\BaseObject;

class MyUtils extends BaseObject
{

    public function __construct($config = [])
    {
        // ... initialization before configuration is applied

        parent::__construct($config);
    }

    public function init()
    {
        parent::init();

        // ... initialization after configuration is applied
    }

    // Returns a file size limit in bytes based on the PHP upload_max_filesize
    // and post_max_size
    public function file_upload_max_size() 
    {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $post_max_size = self::parse_size(ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }   

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = self::parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    public function parse_size($size) 
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }    
    // {{{ limitWords
    /**
     * Returns a string limited to a certain number of words
     *
     * @param string $string
     * @param integer $word_limit
     * @param string $append Text that will b eappended if reduced string has less words than initial string
     * @return string
     */
    public function limitWords($string, $word_limit, $append = ' ...')
    {
        $string = strip_tags($string);
        $words = explode(' ', strip_tags($string));
        $return = trim(implode(' ', array_slice($words, 0, $word_limit)));
        if(count($words) > $word_limit){
            $return .= $append;
	    }
	    return $return;
    } // }}} 
    public function isAssoc($arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ||  is_array ( $node ) ) 
                ? self::xml2array ( $node ) 
                : $node;

        return $out;
    }

    public function sec2hms($seconds)
    {
        $millies = $seconds - round($seconds);
        $t = round($seconds);
        // mit millies: return sprintf('%02d:%02d:%02d,%03d', ($t/3600),($t/60%60), $t%60, $millies*10);
        return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
    }
}
