<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoloaders()
    {
        Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
        ini_set('unserialize_callback_func', 'Zend_Loader::loadClass');
	}

	protected function _initConstants()
    {
    	$options = $this->getOptions();
    	$constants = $options['constants'];

    	foreach($constants as $name => $value){
    		$constantName = strtoupper($name);
    		defined($constantName) || define($constantName, $value);
    	}

    	defined("CURRENT_DIR") || define("CURRENT_DIR", getcwd());
    }

	protected function _initSettings()
	{
	    ini_set('upload_max_filesize', GLOBAL_UPLOAD_MAXSIZE);
	    ini_set('upload_tmp_dir', GLOBAL_UPLOAD_TMPDIR);

		date_default_timezone_set('Europe/Paris');
	}

	protected function _initSession()
	{
		// Flash session ids:
		if (isset($_POST['PHPSESSID']) && !empty($_POST['PHPSESSID'])) {
	        Zend_Session::setId($_POST['PHPSESSID']);
		}

		// Connection from node for chat
		if (isset($_GET['PHPSESSID']) && !empty($_GET['PHPSESSID'])){
	        Zend_Session::setId($_GET['PHPSESSID']);
		}

		// Allow cookies on all subdomains:
		Zend_Session::start(array(
			'cookie_domain' => COOKIE_DOMAIN
		));
	}

    protected function _initMyFrontController()
	{
		$this->bootstrap('FrontController');
		$front = $this->getResource('FrontController');

		$response = new Zend_Controller_Response_Http;
		$front->setResponse($response)
                ->registerPlugin(new Lib_Plugin_Maintenance())
				->setRouter(Globals::getRouter())
				->registerPlugin(new Lib_Plugin_Init_User())
				->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array('controller' => 'error','action' => 'exception')));
	}

	protected function _initApp()
	{
        // HELPERS
        Zend_Controller_Action_HelperBroker::addPrefix('Lib_Controller_Helper');
        Zend_Controller_Action_HelperBroker::addPath('../library/Lib/Controller/Helper', 'Lib_Controller_Helper');

    	error_reporting(E_ALL|E_STRICT);
	}

    protected function _initView()
    {
		$doctypeHelper = new Zend_View_Helper_Doctype();
		$doctypeHelper->doctype(APP_PAGE_DOCTYPE);

        $view = new Zend_View();
        $view->env = APPLICATION_ENV;

        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);

		Zend_Layout::startMvc(array('layoutPath' => '../application/views/layouts'));

        // Return it, so that it can be stored by the bootstrap
        return $view;
    }

	protected function _initDatabase()
	{
	    $db = Globals::getMainDatabase();
        Zend_Db_Table::setDefaultAdapter($db);
        if(ALLOW_CACHE){
        	Zend_Db_Table::setDefaultMetadataCache(Globals::getGlobalCache());
        }

        return $db;
	}

	protected function _initProfiler()
	{
		if(PROFILE){
		    $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
		    $profiler->setEnabled(true);
		    Globals::getMainDatabase()->setProfiler($profiler);
		    $request  = new Zend_Controller_Request_Http();
		    $response = new Zend_Controller_Response_Http();
		    $channel  = Zend_Wildfire_Channel_HttpHeaders::getInstance();
		    $channel->setRequest($request);
		    $channel->setResponse($response);
		}
	}

}
