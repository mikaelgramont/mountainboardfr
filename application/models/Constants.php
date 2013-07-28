<?php
/**
 * Application-wide constants
 */
class Constants
{
    const SUCCESS = 'success';
    const FAILURE = 'failure';
    const ERROR = 'error';

    const HASHKEY = 'mysecrethashkey' ;

    private static $defaultKeywords = array('mountainboard','france','allterrainboard','dirtboard','tout-terrain');
    public static function getDefaultKeywords()
    {
        return self::$defaultKeywords;
    }

    public static $wordfilters = array();
    
    public static $months = array(
         1 => 'january',
         2 => 'february',
         3 => 'march',
         4 => 'april',
         5 => 'may',
         6 => 'june',
         7 => 'july',
         8 => 'august',
         9 => 'september',
        10 => 'october',
        11 => 'november',
        12 => 'december'
    );
}
