<?php
class Lib_View_Helper_AutoCompleteCountry extends Lib_View_Helper_AutoCompleteData
{
    protected $_route = 'getcountry';

    public function autoCompleteCountry($id, $value = null, $params = array(), $attribs = array())
    {
        return $this->_autoComplete($id, $value, $params, $attribs);
    }
}