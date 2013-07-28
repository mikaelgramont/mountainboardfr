<?php
/**
 * Creates 'dummy' user with guest access rights in case
 * no login was previously performed
 *
 */
class Lib_Plugin_Maintenance extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if(MAINTENANCE){
            $this->getResponse()->setRawHeader('HTTP/1.1 503 Service Unavailable');
        	$request->setControllerName('error');
            $request->setActionName('maintenance');
            Zend_Controller_Front::getInstance()->unregisterPlugin('Lib_Plugin_Init_Database');
            Zend_Controller_Front::getInstance()->unregisterPlugin('Lib_Plugin_Init_User');
        }
    }
}