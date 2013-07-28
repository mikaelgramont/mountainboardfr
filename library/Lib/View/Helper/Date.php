<?php
class Lib_View_Helper_Date extends Zend_View_Helper_Abstract
{
    /**
     * Render a date according to the locale
     *
     * @param array $items
     * @return string
     */
    public function date($dateIn, $displayTime = true, $locale = 'fr')
    {
		if(empty($dateIn)){
			$dateIn = date("Y-m-d 00:00:00");
		}
    	$date = new Zend_Date($dateIn, false, $locale);
		if($displayTime){
			$return = $date->toString( Zend_Registry::get('Zend_Locale'));
		} else {
			$return = $date->toString( Zend_Locale_Format::getDateFormat(Zend_Registry::get('Zend_Locale')));
		}
		return $return;
    }
}