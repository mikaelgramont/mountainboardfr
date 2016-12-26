<?php
abstract class Lib_View_Helper_AutoCompleteData extends Zend_View_Helper_FormElement
{
    protected $_route = null;

    public function autoCompleteData($id, $value = null, $params = array(), $attribs = array())
    {
        return $this->_autoComplete($id, $value, $params, $attribs);
    }

    protected function _autoComplete($id, $value = null, $params = array(), $attribs = array())
    {
        if($attribs == null){
            $attribs = array();
        }

        if(empty($this->_route)){
            throw new Lib_Exception("Empty route for autocomplete");
        }

        $params['url'] = Globals::getRouter()->assemble(array(), $this->_route, true);
        if (isset($params['placeholder'])) {
            $attribs['placeholder'] = $params['placeholder'];
            unset($params['placeholder']);
        }
        
        $this->view->JQuery()->addJavascriptFile($this->view->asset()->script('autocomplete.js'));

        return $this->view->autoComplete($id, $value, $params, $attribs);
    }
}