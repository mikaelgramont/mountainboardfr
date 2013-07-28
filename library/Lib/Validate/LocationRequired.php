<?php
class Lib_Validate_LocationRequired extends Zend_Validate_Abstract
{
	const LOCATION_REQUIRED = 'locationRequired';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::LOCATION_REQUIRED => "locationRequired",
    );
    
    /**
     * Defined by Zend_Validate_Interface
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
		if($value){
        	return true;
		} else {
        	$this->_error(self::LOCATION_REQUIRED);
			return false;
        }
    }	
}