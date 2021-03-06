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

// Zend_Application
require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();
define('APP_URL', (USE_CDN ? 'https://' : 'http://').APP_URL_NO_PROTOCOL);

$application->run();
