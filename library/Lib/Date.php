<?php
class Lib_Date
{
    public static function getDateFormat($locale = null)
    {
        if(empty($locale)){
            $locale = Zend_Registry::get('Zend_Locale');
        }

        switch($locale){
            case 'fr':
            case 'en':
        	    $dateFormat = str_replace(
        	        array('EEE', 'EEEE', 'M', 'MM', 'MMM', 'MMMM', 'YY', 'YYYY', 'yyyy'),
        	        array('D', 'DD', 'm', 'mm', 'M', 'MM', 'y', 'yy', 'yy'),
        	        Zend_Locale_Data::getContent($locale, 'date', 'short')
        	    );
                break;
            default:
        	    $dateFormat = str_replace(
        	        array('EEE', 'EEEE', 'M', 'MM', 'MMM', 'MMMM', 'YY', 'YYYY', 'yyyy'),
        	        array('D', 'DD', 'm', 'mm', 'M', 'MM', 'y', 'yy', 'yy'),
        	        Zend_Locale_Format::getDateFormat($locale)
        	    );
                break;
        }
	    return $dateFormat;
    }

    public static function getFormattedDate($date, $useTime = true, $monthsAsString = false, $separators = null, $locale = null)
    {
        if(empty($separators)){
            $separators = array('/', ':');
        }

        if(empty($locale)){
            $locale = Zend_Registry::get('Zend_Locale');
        }

        $translate = Globals::getTranslate();

        $y = substr($date, 0, 4);
        $m = substr($date, 5, 2);
        $d = substr($date, 8, 2);

        if($monthsAsString){
        	$m = self::getTranslatedMonth($m, $translate);
            $separators[0] = ' ';
        }

        switch($locale){
            case 'en':
                $return = $m.$separators[0].$d.$separators[0].$y;
                break;
            case 'fr':
            default:
                $return = $d.$separators[0].$m.$separators[0].$y;
                break;
        }

        if(!$useTime){
            return $return;
        }

        $return .= ' '.$translate->_('at').' ';

        $h = substr($date, 11, 2);
        $m = substr($date, 14, 2);
        $s = substr($date, 17, 2);

        switch($locale){
            case 'en':
                if($h > 12){
                    $append = ' (PM)';
                    $h -= 12;
                } else {
                    $append = ' (AM)';
                }
                break;
            default:
                $append = '';
                break;
        }

        $return .= $h.$separators[1].$m.$separators[1].$s.$append;

        return $return;
    }
    
    public static function getTranslatedMonth($month, $translate = null)
    {
    	if(empty($translate)){
    		$translate = Globals::getTranslate();
    	}
    	
    	$month = (int)$month;
    	
    	if($month < 1 || $month > 12) {
    		return '';
    	}
    	
    	$m = ucfirst($translate->_(Constants::$months[(int)$month]));
    	return $m;
    }
    
    public static function getOrdinalDay($day, $locale = null)
    {
    	if(empty($locale)){
    		$locale = Zend_Registry::get('Zend_Locale');
    	}
    	
        switch($locale){
            case 'en':
				if(substr($day, -1) == '1' && $day != '11'){
					$return = $day .'st';
				} elseif(substr($day, -1) == '2' && $day != '12') {
	            	$return = $day .'nd';
				} else {
	            	$return = $day .'th';
	            }
            	break;
            default:
                $return = $day;
                break;
        }
    	
    	return $return;
    }
}