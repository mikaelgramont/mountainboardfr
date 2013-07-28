<?php
require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

class ApplicationTest extends Zend_Test_PHPUnit_ControllerTestCase
{	
	public $application;
	
    protected function setUp()
    {
    	$this->application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
    	
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
        
        $this->resetRequest();
        $this->resetResponse();
    	
        Globals::resetAll();
        $this->_cleanUpCache();
        
        Lib_Translate_Factory::build();
    }

    protected function tearDown()
    {
    	$this->_cleanUpCache();
    	parent::tearDown();
    }
    
    protected function _cleanUpCache()
    {
		return;
    	$cacheFolders = array(
			GLOBAL_CACHE_FILE_DIR,
			APP_CACHE_FILE_DIR,
			APP_CACHE_HTMLPURIFIER_DIR,
		); 
		foreach($cacheFolders as $folder){
	        $command = "find $folder -maxdepth 1 -type f -delete";
			shell_exec($command);
		}
    }
    
    public function appBootstrap()
    {
   		$this->application->bootstrap();
    }    

    public function dispatchWithExceptions($url = null)
    {
        // redirector should not exit
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->setExit(false);

        // json helper should not exit
        $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
        $json->suppressExit = true;

        $request    = $this->getRequest();
        if (null !== $url) {
            $request->setRequestUri($url);
        }
        $request->setPathInfo(null);
        $controller = $this->getFrontController();
        $this->frontController
             ->setRequest($request)
             ->setResponse($this->getResponse())
             ->throwExceptions(true)
             ->returnResponse(false);
        $this->frontController->dispatch();
    }    
}
