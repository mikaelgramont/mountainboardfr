<?php
define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_PATH', BASE_PATH . '/application');

// Include path
set_include_path(
    BASE_PATH . '/library'
    . PATH_SEPARATOR . BASE_PATH . '/application/models'
    . PATH_SEPARATOR . BASE_PATH . '/library/HTMLPurifier'
    . PATH_SEPARATOR . BASE_PATH . '/library/sphinx'
);

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
              (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                                         : 'development'));


/**
 * @todo: execute constants
 */
require_once('Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);

$config = new Zend_Config_Ini('../application/configs/application.ini', APPLICATION_ENV);
$data = $config->toArray();
$constants = $data['constants'];

foreach($constants as $name => $value){
	$constantName = strtoupper($name);
	defined($constantName) || define($constantName, $value);
}



$db = Globals::getMainDatabase();
$db->query("SET NAMES ".GLOBAL_DB_ENCODING);
Zend_Db_Table::setDefaultAdapter($db);
if(ALLOW_CACHE){
	Zend_Db_Table::setDefaultMetadataCache(Globals::getGlobalCache());
}

try{
    require_once("../application/controllers/AnonymousAjaxController.php");
    $controller = new AnonymousAjaxController();
    $actionName = isset($_GET['action']) ? $_GET['action'] : '';
    if(empty($actionName)){
    	exit();
    }
    $action = $actionName.'Action';
    if(!method_exists($controller, $action)){
        Globals::getLogger()->Log("No method '$action' for controller '$controller'", Zend_Log::ERR);
        die();
    }

    // Controller
    $result = $controller->$action();
    if(is_array($result)){
    	extract($result);
    }
    
    // View
    $viewPath = "../application/views/scripts/anonymousajax/";
    $view = $viewPath.$actionName.'.phtml';
    require($view);

} catch (Exception $e) {
    $logMessage  = get_class($e).PHP_EOL;
    $logMessage .= "Code: ".$e->getCode().PHP_EOL;
    $logMessage .= "Message: ".$e->getMessage().PHP_EOL.$e->getTraceAsString();
    Globals::getLogger()->Log($logMessage, Zend_Log::ERR);
    die();
}
