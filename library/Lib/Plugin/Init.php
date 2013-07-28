<?php
class Lib_Plugin_Init extends Zend_Controller_Plugin_Abstract
{
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        // MVC AND VIEW CONFIG
        $doctypeHelper = new Zend_View_Helper_Doctype();
        $doctypeHelper->doctype(APP_PAGE_DOCTYPE);

        // HELPERS
        Zend_Controller_Action_HelperBroker::addPrefix('Lib_Controller_Helper');
        Zend_Controller_Action_HelperBroker::addPath('../models/Lib/Controller/Helper', 'Lib_Controller_Helper');
    }
}