<?php
class AdminController extends Lib_Controller_Action
{
    public function init()
    {
        if($this->_request->getParam('authCheck') !== AUTHCHECK){
    		die('0');
    	}

    	parent::init();
    }

	public function indexAction()
    {
		die('Admin not implemented');
    }

    /**
     * Called from commandline script or directly
     */
    public function clearApcCacheAction()
    {
    	$mode = $this->_request->getParam('mode');
    	if(!in_array($mode, array('user', 'opcode'))){
    		die('1');
    	}

    	apc_clear_cache($mode);
        echo('ok');
        exit();
    }

    /**
     * Called from commandline script or directly
     */
    public function clearMemcacheAction()
    {
        $memcache = new Memcache();
        $memcache->connect('127.0.0.1',11211);
        if (!$memcache) {
                echo ("Could not connect");
                exit();
        }
        if (!$memcache->flush()) {
                echo ("Could not flush");
        }
        echo('ok');
        exit();
    }

	public function generateAssetVersionsAction()
	{
		Lib_AssetCache::buildLookupTable();
		$assets = Lib_AssetCache::getLookupTable();
		die("<pre>".var_export($assets, true)."</pre>");
	}

	public function impersonateAction()
	{
	    if($this->_user->status != User::STATUS_ADMIN){
	    	die('0');
	    }

		if(!$userId = $this->_request->getParam('i')){
    		die('1');
    	}

    	$table = new User();
    	$res = $table->find($userId);
    	if(empty($res)){
    		die('2');
    	}
    	$user = $res->current();
    	$name = $user->getTitle();
    	Globals::getLogger()->security("Impersonating user '{$userId} ('{$name}')'");

    	$userIdColumn = User::COLUMN_USERID;
        $lastLoginColumn = User::COLUMN_LAST_LOGIN;

        $userData = new stdClass();
        $userData->session_id = session_id();
        $userData->$userIdColumn = $userId;
        $userData->lastLogin = $user->$lastLoginColumn;
        Zend_Auth::getInstance()->getStorage()->write($userData);

        $this->_redirect('/');
	}
}