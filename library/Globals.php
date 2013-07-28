<?php
/**
 * Class used to retrieve application-wide objects and data (registry)
 */
class Globals
{
	/**
	 * Database object (singleton)
	 * @var Zend_Db_Adapter
     */
    private static $_db;
    /**
     *
     */
    private static $_routeConfig;
    /**
	 * Routing configuration (singleton)
	 * @var Zend_Controller_Router_Rewrite
     */
    private static $_router;
    /**
     * User object
     *
     * @var User_Row
     */
    private static $_user;
    /**
     * Acl
     *
     * @var Lib_Acl
     */
    private static $_acl;
    /**
     * Login error data for display
     *
     * @var array
     */
    private static $_loginErrorData = array();
	/**
	 * Logger object (singleton)
	 *
	 * @var Zend_Log
     */
    private static $_logger;
	/**
	 * Cache object (singleton)
	 *
	 * @var Zend_Log
     */
    private static $_globalCache;
	/**
	 * Cache object (singleton)
	 *
	 * @var Zend_Log
     */
    private static $_appCache;
    /**
     * Global BreadCrumbs object
     *
     * @var BeadCrumbs
     */
    private static $_breadCrumbs;
    /**
     * Global Translate object
     *
     * @var Zend_Translate
     */
    private static $_translate;
    /**
     * Application name
     *
     * @var string
     */
    private static $_app;
    /**
     * HTMLPurifier instance
     *
     * @var HTMLPurifier
     */
    private static $_htmlPurifier;

    /**
     * List of allowed file extensions for upload
     *
     * @var array
     */
    private static $_filesExtensionUploadWhiteList = array(
    	'jpg','jpeg','gif','png','swf','pdf','ppt','doc','xls','txt'
    );

    /**
	 * Returns a database connection
	 *
     * @return Zend_Db_Adapter_Abstract
  	 */
    public static function getMainDatabase()
    {
		if(empty(self::$_db)){
            $parameters = array('host'     => GLOBAL_DB_HOST,
    			                'username' => GLOBAL_DB_USER,
    			                'password' => GLOBAL_DB_PASSWORD,
    			                'dbname' => GLOBAL_DB_NAME ,
    			                'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.GLOBAL_DB_ENCODING.';')
    			                );
			$db = Zend_Db::factory('Pdo_Mysql', $parameters);
    		$db->getConnection();

			self::$_db = $db;
		}
		return(self::$_db);
	}

	/**
	 * Build and save in cache the route configuration,
	 * from the specified file and format.
	 *
	 * @param string $app
	 * @param string $file
	 * @param string $format
	 * @throws Lib_Exception
	 * @return Zend_Config
	 */
	public static function getRouteConfig($file, $format)
	{
		switch($format){
			case 'xml':
				$content = new Zend_Config_Xml($file);
				break;
			case 'ini':
				$content = new Zend_Config_Ini($file);
				break;
			default:
				throw new Lib_Exception('Route config format not supported: '.$format);
        }
        return $content;
	}

	/**
	 * Return the router object for the current module
	 *
	 * @param string $app
	 * @throws Lib_Exception
     * @return Zend_Controller_Router_Rewrite
  	 */
	public static function getRouter($app = null)
	{
        if(empty(self::$_router)){
        	$cacheId = 'routes_'.ROUTES_CONF_FORMAT;
        	if($app){
        		$cacheId .= '_'.$app;
        	}

 	    	$cache = self::getAppCache();
        	if(!ALLOW_CACHE){
				$router = new Zend_Controller_Router_Rewrite();
				$routes = self::getRouteConfig('../application/configs/routes.'.ROUTES_CONF_FORMAT, ROUTES_CONF_FORMAT);
				$router->addConfig($routes, 'routes');
        	} else {
	        	if(!$router = $cache->load($cacheId)){
					$router = new Zend_Controller_Router_Rewrite();
					$routes = self::getRouteConfig('../application/configs/routes.'.ROUTES_CONF_FORMAT, ROUTES_CONF_FORMAT);
					$router->addConfig($routes, 'routes');
					$cache->save($router, $cacheId);
	        	}
            }

			self::$_router = $router;
		}
        return self::$_router;
	}

    /**
     * Return the User object
     *
     * @return User
     * @throws Lib_Exception_User
     */
	public static function getUser()
    {
        return self::$_user;
    }

    /**
     * Sets the User objectt
     *
     * @param User_Row $user
     */
    public static function setUser($user)
    {
        self::$_user = $user;
    }

    public static function getAcl()
    {
        if(empty(self::$_acl)){
            $user = self::getUser();
            self::$_acl = new Lib_Acl($user);
        }
        return self::$_acl;
    }

    /**
     * Sets whether to use zlib compression
     *
     * @param boolean $boolean
     */
    public static function setCompression($boolean)
    {
        ini_set('zlib.output_compression', $boolean);
    }

    public static function setLoginErrorData($arr)
    {
        self::$_loginErrorData = $arr;
    }

	public static function getLoginErrorData()
    {
        return self::$_loginErrorData;
    }

    /**
     * Configure and return the logger instance
     *
     * @return Zend_Log
     */
    public static function getLogger()
    {
        if(empty(self::$_logger)){
            if(!empty(self::$_user)){
                $userId = self::$_user->{User::COLUMN_USERID};
            } else {
                $userId = 'null';
            }

            $logger = new Logger($userId);
			/*
            $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'local';

            $logger = new Zend_Log();
            $logger->setEventItem('timestamp', date('Y-m-d H:i:s'));
            $logger->setEventItem(User::COLUMN_USERID, $userId);
            $logger->setEventItem('url', Utils::getCompleteUrl());
            $logger->setEventItem('referer', array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : '');
            $logger->setEventItem('ip', $ip);
            $logger->setEventItem('hostname', Utils::getHost($ip));

            $file = self::_getLogFile();

            $format = '%timestamp% [%priorityName% (%priority%)] / '.User::COLUMN_USERID.': \'%'.User::COLUMN_USERID.'%\' / IP: %ip% / HOSTNAME: %hostname%'.PHP_EOL.'URL: %url%'.PHP_EOL.'REFERER: %referer%'.PHP_EOL.'%message%' . PHP_EOL.PHP_EOL.PHP_EOL;
            $formatter = new Zend_Log_Formatter_Simple($format);

            $writer = new Zend_Log_Writer_Stream($file);
            $writer->setFormatter($formatter);

            $logger->addWriter($writer);
			*/
            self::$_logger = $logger;
        }

        return self::$_logger;
    }

    private static function _getLogFile()
    {
        $file = APP_DEBUGDIR . date('Y-m-d') . '.log';
        return $file;
    }

    /**
     * Get the global cache instance
     *
     * @return Zend_Cache_Core
     */
    public static function getGlobalCache()
    {
        if(empty(self::$_globalCache)){
        	$cache = Cache::factory(GLOBAL_CACHE_METHOD, array('cache_dir' => GLOBAL_CACHE_FILE_DIR));
            self::$_globalCache = $cache;
        }
        return self::$_globalCache;
    }

    /**
     * Get the application cache instance
     *
     * @return Zend_Cache_Core
     */
    public static function getAppCache()
    {
        if(empty(self::$_appCache)){
        	$cache = Cache::factory(APP_CACHE_METHOD, array('cache_dir' => APP_CACHE_FILE_DIR));
        	self::$_appCache = $cache;
        }
        return self::$_appCache;
    }

    /**
     * Get the BreadCrumbs instance
     *
     * @return BreadCrumbs
     */
    public static function getHTMLPurifier()
    {
        if(empty(self::$_htmlPurifier)){
			$config = self::_getHTMLPurifierConfig();
			self::$_htmlPurifier = new HTMLPurifier($config);
        }
        return self::$_htmlPurifier;
    }

    /**
     * Get the Zend_Translate instance
     * After object has been initialized, it can
     * be updated by passing a locale string
     *
     * @return Zend_Translate
     */
    public static function getTranslate($updateTo = null)
    {
        if($updateTo !== null) {
            self::$_translate = Lib_Translate_Factory::build($updateTo);
        } elseif(empty(self::$_translate)) {
            self::$_translate = Lib_Translate_Factory::build();
        }
        return self::$_translate;
    }

    /**
     * Reset all static data
     * Used for unit testing
     */
    public static function resetAll()
    {
        self::$_db = null;
        self::$_router = null;
        self::$_user = null;
        self::$_loginErrorData = null;
        self::$_logger = null;
        self::$_globalCache = null;
        self::$_appCache = null;
        self::$_breadCrumbs = null;
    }

    public static function getAppName()
    {
        return self::$_app;
    }

    public static function setAppName($app)
    {
        self::$_app = $app;
    }

	/**
	 * Build and save in cache the categories/sucategories
	 * hierarchy.
	 *
	 * @param string $app
	 * @param string $file
	 * @return Zend_Config
	 */
	public static function getMenuConfig($file = null)
	{
        $file = '../application/configs/menu.xml';
		$cache = self::getAppCache();
        $cacheId = 'menuConfig';
        $config = null;

		if(ALLOW_CACHE || !($config = $cache->load($cacheId)) ){
			$config = new Zend_Config_Xml($file);
			if(ALLOW_CACHE){
				$cache->save($config, $cacheId);
			}
		}

        return $config;
	}

    /**
     * Get the chosen language from subdomain
     *
     * @return string|null
     */
    public static function getSubdomainLanguage()
    {
        return 'fr';

    	if(!isset($_SERVER['HTTP_HOST'])){
            throw new Lib_Exception('No info found for translation');
        }

        $domain= $_SERVER['HTTP_HOST'];
        $regex = '/^([a-zA-Z0-9-]+)\.'.GLOBAL_DOMAIN.'\.'.GLOBAL_EXTENSION.'$/';
        $matches = null;
        preg_match($regex, $domain, $matches);

        // Do not return www for example:
        if(isset($matches[1]) && $matches[1] != APP_SUBDOMAIN){
            return $matches[1];
        }

        return null;
    }

    public static function getDefaultSiteLanguage()
    {
    	return self::getDefaultSiteLanguage();
    }

	private static function _getHTMLPurifierConfig()
	{
    	require_once 'HTMLPurifier.standalone.php';
    	$config = HTMLPurifier_Config::createDefault();
    	$config->autoFinalize = false;

    	$config->set('Core.Encoding', strtoupper(APP_PAGE_ENCODING)); // replace with your encoding
    	$config->set('HTML.Doctype', APP_PAGE_DOCTYPE_AS_STRING); // replace with your doctype
	    if(CACHE_HTMLPURIFIER_ACTIVE){
    		$config->set('Cache.SerializerPath', APP_CACHE_HTMLPURIFIER_DIR);
    	} else {
    		$config->set('Cache.DefinitionImpl', null);
    	}

    	// Allow <a name="foor">bar</a>: allowing id works
    	$config->set('Attr.EnableID', true);
		/*
		 * How to allow <div bla="">
			$config->set('HTML.DefinitionID', 'enduser-customize.html tutorial');
			$config->set('HTML.DefinitionRev', 1);
			$def = $config->getHTMLDefinition(true);
			$def->addAttribute('div', 'bla', 'CDATA');
    	*/
		$config->set('Filter.YouTube', true);

		$dailyMotion = new HTMLPurifier_Filter_DailyMotion();
		$vimeo = new HTMLPurifier_Filter_Vimeo();
		$config->set('Filter.Custom', array($vimeo, $dailyMotion));

		$config->finalize();
    	return $config;
	}

	public static function getFileExtensionUploadWhiteList()
	{
		return self::$_filesExtensionUploadWhiteList;
	}
}
