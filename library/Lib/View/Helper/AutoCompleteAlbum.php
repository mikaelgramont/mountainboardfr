<?php
class Lib_View_Helper_AutoCompleteAlbum extends Lib_View_Helper_AutoCompleteData
{
    protected $_route = 'getalbum';

    public function autoCompleteAlbum($id, $value = null, $params = array(), $attribs = array())
    {
        return $this->_autoComplete($id, $value, $params, $attribs);
    }
}