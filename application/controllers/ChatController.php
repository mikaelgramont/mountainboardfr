<?php
class ChatController extends Lib_Controller_Action
{
    public function init()
    {
        if($this->_request->getParam('authCheck') !== AUTHCHECK){
    		die('0');
    	}

    	parent::init();
    	$this->_helper->viewRenderer->setNoRender();
    	$this->_helper->layout->disableLayout();
    }

    public function indexAction()
    {
		if(!$this->_user->isLoggedIn()){
			$data = array(
				'id' => 0
			);
		} else {
	    	$data = array(
				'id' => $this->_user->getId(),
				'name' => $this->_user->getTitle(),
				'lang' => $this->_user->lang,
				'link' => APP_URL.$this->_user->getLink()
			);
		}
		header('Content-type: text/json');
		die(Zend_Json::encode($data));
    }
}