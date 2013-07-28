<?php
/**
 * Autocomplete username picker
 *
 */
class Lib_Form_Element_Date extends Zend_Form_Element_Text
{
    public $helper = 'myDatePicker';

    /**
     * Will not be used - to circumvent bug in ZendX_JQuery_View_Helper_DatePicker datePicker prototype
     * public function datePicker($id, $value = null, array $params = array(), array $attribs = array())
     * should be
     * public function datePicker($id, $value = null, array $params = array(), $attribs = array())
     *
     * @var array
     */
    public $options = array();

    /**
     * Translate raw date into a locale-compliant date
     *
     * @param string $value
     * @return string
     */
    public function getValueFromDatabase($value)
    {
        if(empty($value)){
            return '';
        }

        if($value == '0000-00-00'){
            return '';
        }

        $parts = explode('-', $value);
        if(count($parts) != 3){
            return '';
        }

        switch(Zend_Registry::get('Zend_Locale')){
            case 'fr':
                $return = $parts[2].'/'.$parts[1].'/'.$parts[0];
                break;
            default:
                $return = $parts[1].'/'.$parts[2].'/'.$parts[0];
                break;
        }
        return $return;
    }

    /**
     * Translate a locale-compliant date into a raw date
     *
     * @param string $value
     * @return string
     */
    public function getFormattedValueForDatabase($value)
    {
        if(empty($value)){
            return '';
        }

        $parts = explode('/', $value);
        switch(Zend_Registry::get('Zend_Locale')){
            case 'fr':
                $return = $parts[2].'-'.$parts[1].'-'.$parts[0];
                break;
            default:
                $return = $parts[2].'-'.$parts[0].'-'.$parts[1];
                break;
        }
        return $return;
    }
}