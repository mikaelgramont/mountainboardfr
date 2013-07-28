<?php
/**
 * Database initialization
 */
class Lib_Plugin_Init_Database extends Zend_Controller_Plugin_Abstract
{
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $db = Globals::getMainDatabase();
        Zend_Db_Table::setDefaultAdapter($db);
        if(ALLOW_CACHE){
        	Zend_Db_Table::setDefaultMetadataCache(Globals::getGlobalCache());
        }
    }
}