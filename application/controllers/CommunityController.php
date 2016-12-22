<?php
class CommunityController extends Lib_Controller_Action
{
    /**
     * Initialisations
     */
    public function init()
    {
        parent::init();
        Zend_Registry::set('Category', Category::COMMUNITY);
    }

    public function indexAction()
    {
		$this->view->wrapperIsCard = true;
    }

    public function nearbyAction()
    {
        Zend_Registry::set('SubCategory', SubCategory::DPT);
		/**
         * Liste des trucs à proximité: tout ce qui se trouve dans le département,
         * avec une carte pour le montrer
         */
    	
    }

    /**
     * List of dpt
     */
    public function dptlistAction()
    {
        Zend_Registry::set('SubCategory', SubCategory::DPT);
    	$redirect = false;
        $country = $this->_request->getParam('country');
        
    	$gotoDptForm = new Dpt_Form_GoTo($country);
        $data = $this->_request->getPost();
    	if($data){
            $gotoDptForm->populate($data);
            $redirect = $gotoDptForm->isValid($data);
        }

        if($redirect){
            $dpt = $gotoDptForm->getElement('dpt')->getDpt($data['dpt']);
            $this->_helper->redirectToRoute('displaydpt',array(
				'name' => $dpt->getCleanTitle(),
				'id' => $dpt->id,
            ));
        }

        $table = new Dpt();
        $where = $this->_acl->isAllowed($this->_user, Lib_Acl::ADMIN_RESOURCE) ? '1' : "status = '".Data::VALID."'";
		$where .= $table->getAdapter()->quoteInto(" AND country = ?", $country);
        $this->view->items = $table->fetchAll($where);
        $this->view->form = $gotoDptForm;
    }

    public function userlistAction()
    {
		$this->_useAdditionalContent = true;
		$this->_helper->layout->setLayout('two-columns');
		$this->view->wrapperIsCard = true;
		
    	$page = $this->_getParam('page', 1);
    	Zend_Registry::set('SubCategory', SubCategory::USERS);
        $table = new User();
        $users = $table->select();
        if(!$this->_acl->isAllowed($this->_user, Lib_Acl::PUBLIC_EDIT_RESOURCE)){
            $statusList = array(
            	User::STATUS_ADMIN,
            	User::STATUS_EDITOR,
            	User::STATUS_WRITER,
            	User::STATUS_MEMBER,
			);
			$string = implode("','", $statusList);
        	$users->where("status IN ('$string')"); 
        }
        $users->order("date DESC");

        $this->view->page = $page;
        $this->view->limit = USERS_PER_PAGE;
        $this->view->users = $users;
    	$this->view->wrapperIsCard = false;
        $this->view->separateFirstContentCardHeader = true;
    }
}