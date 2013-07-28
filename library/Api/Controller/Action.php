<?php
abstract class Api_Controller_Action extends Zend_Controller_Action
{
    /**
     * Name of the resource
     * @var string
     */
	protected $_resourceName = null;

	public $listStart = 0;

	public $listCount = 12;

	public $listDir = 'ASC';

	public $listKey = 'id';

	public function init()
	{
		parent::init();

		$this->_resourceName = $this->_mapResource($this->_request->getControllerName());
		$this->_table = new $this->_resourceName();

		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');

		$accept = $this->getRequest()->getHeader('accept');
		$headers = apache_request_headers();
		//$this->view->headers = var_export($headers, true);
		$html = (isset($headers['Accept']) && strpos($headers['Accept'], 'text/html') !== false);
		if($html){
			$this->view->addHelperPath('../library/Lib/View/Helper/', 'Lib_View_Helper');
        	$this->view->baseUrl = $baseUrl = $this->_request->getBaseUrl();
        	/**
        	 * @todo set content type to text/html
        	 */
        	$this->getResponse()->setRawHeader('Content-Type: text/html');
		} else {
			$viewRenderer->setViewScriptPathSpec(':action.json');
			$this->getResponse()->setRawHeader('Content-Type: application/json');
		}

		$userTable = new User();
		$userId = isset($_SESSION[User::COLUMN_USERID]) ? $_SESSION[User::COLUMN_USERID] : 0;
       	$results = $userTable->find($userId);
	    if($results && $user = $results->current()){
       		Globals::setUser($user);
		} else {
			throw new Exception("Could not find user '$userId'");
		}

		$_SESSION[User::COLUMN_USERID] = $user->{User::COLUMN_USERID};
	}

    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function listAction()
    {
    	/**
    	 * @todo return a paginated and ordered list, selecting only valid elements (move valid clause to rowset)
    	 */
    	$this->view->resources = $this->_table->fetchAll(null, null, 15);
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {
    	/**
    	 * @todo handle error cases and return an error, return valid users ondly
    	 */
    	$id = $this->_request->getParam('id');
    	$result = $this->_table->find($id);
    	$this->view->resource = $result->current();
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction()
    {
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction()
    {
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction()
    {
    }

	/**
	 * Returns the name of the table that corresponds to the resource in the url
	 * @param string $controllerName
	 */
	protected function _mapResource($controllerName)
	{
		$resources = array(
			'riders' => 'User'
		);

		if(!isset($resources[$controllerName])){
			throw new Exception('Unknown resource');
		}

		return 'Api_'.$resources[$controllerName];
	}
}