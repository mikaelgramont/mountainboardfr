<?php
class Lib_View_Helper_AutoCompleteTrick extends Lib_View_Helper_AutoCompleteData
{
    protected $_route = 'gettrick';

    public function autoCompleteTrick($id, $value = null, $params = array(), $attribs = array())
    {
        return $this->_autoComplete($id, $value, $params, $attribs);
    }
}