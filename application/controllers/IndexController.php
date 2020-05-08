<?php
class IndexController extends Lib_Controller_Action
{
    /**
     * Application index page
     *
     */
    public function indexAction()
    {
        Zend_Registry::set('SubCategory', SubCategory::INDEX);

    	$seeInvalidItems = $this->_acl->isAllowed($this->_user, Lib_Acl::PUBLIC_EDIT_RESOURCE);

        $this->view->form = $this->_getForm();
        $table = new Item();
        $this->view->items = $table->getArticles($this->_request->getParam('page', 1), null, $seeInvalidItems);

        $this->view->isHomePage = true;
        $this->_helper->layout->setLayout('two-columns');

        $this->view->additionalContentItems = $this->_helper->getAdditionalContentItems->homePage($this->_user, $this->_acl);
        //$this->_useHeaderSlideshow = true;
    }

    public function switchlanguageAction()
    {
    	$switchTo = $this->_request->getParam('lang');
    	Lib_Translate_Factory::build($switchTo);

    	$from = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : APP_URL.'/';
    	$this->getResponse()->setRedirect($from)
                 			->sendResponse();
        exit();
    }

    public function publicrssAction()
    {
    	header('Content-Type: text/xml; charset=UTF-8');
    	$table = new Item();
    	$this->view->items = $table->getArticles(1);
    	$this->_helper->layout()->disableLayout();
    }

    public function contactAction()
    {
    	$form = new Lib_Form_Contact($this->_user);
        $this->view->display = 'form';
        $this->view->form = $form;
        return;

        $data = $this->_request->getPost();
        if(!$data || !$form->isValid($data)){
            $this->view->display = 'form';
        	$this->view->form = $form;
            return;
        }

        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'local';
		$emailStatus = $this->_helper->emailer()->sendEmail(
			APP_EMAIL_CONTACT,
			array(
				'message' => $data['message'],
				'ip' => $ip,
				'hostname' => Utils::getHost($ip),
				'id' => $this->_user->{User::COLUMN_USERID},
				'name' => $this->_user->{User::COLUMN_USERNAME},
			),
			Lib_Controller_Helper_Emailer::CONTACT_EMAIL
		);

		if($emailStatus){
			$this->view->display = 'success';
		} else {
			$this->view->display = 'failure';
		}
    }

    /**
     * Login / Logout form
     */
    protected function _getForm()
    {
        if($this->_user->{User::COLUMN_STATUS } > User::STATUS_GUEST){
            // Logout form
            $form = new User_Form_Logout(array(), Globals::getRouter()->assemble(array(), 'logout', true));
            return $form;
        }

        // Login form
        $form = new User_Form_Login(array(), Globals::getRouter()->assemble(array(), 'index', true));
        $data = $this->_request->getPost();

        if (!$data){
            return $form;
        }

        if($form->isValid($data)){
            // Form is valid, we must now log in
            if($form->chosenAuthMethod == User::LOGIN_AUTHMETHOD_OPENID && OPENID_ACTIVE){
                $this->_forward('openidlogin','user');
            } else {
                $this->_forward('login','user');
            }
        }
        return $form;
    }
}
