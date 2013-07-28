<?php
class Lib_Validate_Video extends Zend_Validate_Abstract
{
    const NOT_VALID = 'videoCodeNotValid';

	/**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_VALID => "videoCodeNotValid",
    );

    public function isValid($value)
    {
    	$matches = null;
    	$regex = Media_Item_Video::getCleanVideoCodeRegex(); 
		$matchCount = preg_match_all($regex,$value,$matches);
		if(!$matchCount){
    		$this->_error(self::NOT_VALID);
        	return false;
		}
		
		return true;
    }	
}