<?php
class Lib_Controller_Helper_RedirectToRoute extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Redirect to a given route immediately
     *
     * @param string $route
     * @param array $params
     * @param boolean $reset
     */
    public function direct($route, $params = array(), $reset = true)
    {
        if(!empty($this->_actionController)){
            $response = $this->_actionController->getResponse();
        } else {
            $response = new Zend_Controller_Response_Http();
        }
        $response->setRedirect(Globals::getRouter()->assemble($params, $route, $reset))
                 ->sendResponse();
        exit();
    }
}