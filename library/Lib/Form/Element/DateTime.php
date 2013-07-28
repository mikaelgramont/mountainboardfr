<?php
/**
 * Autocomplete username picker
 *
 */
class Lib_Form_Element_DateTime extends Zend_Form_Element_Text
{
    /**
     * Translate raw datetime into a locale-compliant datetime
     *
     * @param string $value
     * @return string
     */
    public function getValueFromDatabase($value)
    {
        if(empty($value)){
            return '';
        }

        if($value == '0000-00-00 00:00:00'){
            return '';
        }

        $parts1 = explode(' ', $value);
        $ymd = $parts1[0];

        $parts = explode('-', $ymd);
        if(count($parts) != 3){
            return '';
        }

        switch(Zend_Registry::get('Zend_Locale')){
            case 'fr':
                $return = $parts[2].'/'.$parts[1].'/'.$parts[0]. ' '.$parts1[1];
                break;
            default:
                $return = $parts[1].'/'.$parts[2].'/'.$parts[0]. ' '.$parts1[1];
                break;
        }
        return $return;
    }

    /**
     * Translate a locale-compliant datetime into a raw datetime
     *
     * @param string $value
     * @return string
     */
    public function getFormattedValueForDatabase($value)
    {
        if(empty($value)){
            return null;
        }

        $parts1 = explode(' ', $value);
        $ymd = $parts1[0];

        $parts = explode('/', $ymd);
        switch(Zend_Registry::get('Zend_Locale')){
            case 'fr':
                $return = $parts[2].'-'.$parts[1].'-'.$parts[0]. ' '.$parts1[1];
                break;
            default:
                $return = $parts[2].'-'.$parts[0].'-'.$parts[1]. ' '.$parts1[1];
                break;
        }
        return $return;
    }
}