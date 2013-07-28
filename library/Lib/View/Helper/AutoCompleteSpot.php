<?php
class Lib_View_Helper_AutoCompleteSpot extends Lib_View_Helper_AutoCompleteData
{
    protected $_route = 'getspot';

    public function autoCompleteSpot($id, $value = null, $params = array(), $attribs = array())
    {
        return $this->_autoComplete($id, $value, $params, $attribs);
    }
}