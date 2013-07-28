<?php
class Lib_View_Helper_AutoCompleteDpt extends Lib_View_Helper_AutoCompleteData
{
    protected $_route = 'getdpt';

    public function autoCompleteDpt($id, $value = null, $params = array(), $attribs = array())
    {
        if($attribs == null){
            $attribs = array();
        }

        if(empty($this->_route)){
            throw new Lib_Exception("Empty route for autocomplete");
        }

        $params['url'] = Globals::getRouter()->assemble(array(), $this->_route, true);
        if(isset($params['country']) && $params['country']){
    		$params['url'] .= '?country='.$params['country'];
    		unset($params['country']);
    	}

    	$this->view->JQuery()->addJavascriptFile($this->view->asset()->script('autocomplete.js'));

        return $this->view->autoComplete($id, $value, $params, $attribs);
    }
}