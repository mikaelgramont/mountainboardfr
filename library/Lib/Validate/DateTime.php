<?php
class Lib_Validate_DateTime extends Zend_Validate_Abstract
{
    const NOT_VALID = 'notValid';

	/**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_VALID => "dateTimeNotValid",
    );

    public function isValid($value)
    {
        switch(Zend_Registry::get('Zend_Locale')){
            case 'fr':
            default:
                $preg = '/^\d{2}(\-|\/|\.)\d{2}(\-|\/|\.)\d{4} \d{2}:\d{2}:\d{2}$/';
                break;
        }

        if (!preg_match($preg, $value)) {
            $this->_error(self::NOT_VALID);
            return false;
        }
        return true;
    }
}