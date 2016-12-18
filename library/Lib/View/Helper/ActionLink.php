<?php
/**
 * This helper builds the link to take an action.
 * It adds links to login/registration and facebook connect,
 * if the user is not logged in.
 */
class Lib_View_Helper_ActionLink extends Zend_View_Helper_Abstract
{
	protected $_hasRenderedActionLinkJS = false;
    
    public function actionLink($prepend, $append, User_Row $user,
            array $routingInfo, $class="", $id="", $renderAsHeaderAction=false)
	{
		$return  = $prepend;
		
		if($user->isLoggedIn()){
			if ($renderAsHeaderAction) {
    		    $class .= "class=\"headerCardAction headerCardActionInMenu\"";
    		}
    		if(isset($routingInfo['url'])){
				$return .= $this->view->getHelper('routeLink')
				    ->link($routingInfo['url'], $routingInfo['title'], $class, $id);
			} else {
				$return .= $this->view->routeLink(
				    $routingInfo['route'], $routingInfo['title'],
				    $routingInfo['params'], $class, $id);
			}

			$return .= $append;
			return $return;
		}

		$router = Globals::getRouter();
		if(isset($routingInfo['url'])){
			$destination = $routingInfo['url'];
			$title = $routingInfo['title'];
		} else {
			$destination = $router->assemble($routingInfo['params'],
			    $routingInfo['route'], true);
			$title = $this->view->translate($routingInfo['route']);
		}
		
		$loginButtonClass = 'actionLink';
    	if ($renderAsHeaderAction) {
		    $loginButtonClass .= " headerCardAction headerCardActionInMenu";
		}
		
		$loginUrl = $router->assemble(array(), 'login', true);
		$return .= "<a class=\"$loginButtonClass\" href=\"".$loginUrl.'">'.ucfirst($title).'</a>'.PHP_EOL;
		$return .= $append;
		$modalLogin  = '	<p class="modalTitle">'.ucfirst($this->view->translate('actionLinkModalInstructions')).'</p>'.PHP_EOL;
		$oauth = new Facebook_Oauth(array(
			'scope' => array('email'),
			'destination' => APP_URL.$destination,
		));
		$fbUrl = $oauth->buildInitialUrl();
		$class = empty($class) ? '' : " class=\"$class\"";
		$id = empty($id) ? ' id="facebookConnect"' : " id=\"$id\"";
		$fbLink = "<a$class$id href=\"".$fbUrl.'">'.ucfirst($this->view->translate('fbConnect')).'</a>'.PHP_EOL;
		$fbLink = $this->_facebookLink($fbUrl, $this->view->translate('fbConnect'), $id, $class);
		$modalLogin .= $this->loginRegistrationMarkup($user, false, true, $destination, $fbLink).PHP_EOL;

		$this->view->modalContent->addItem('login', $modalLogin);
		$this->_maybeRenderActionLinkJS();

		return $return;
	}

	protected function _maybeRenderActionLinkJS()
	{
	    if ($this->_hasRenderedActionLinkJS) {
	        return;
	    }
	    
		$js = <<<JS
Lib.Event.listenOnClass('click', 'actionLink', function(e) {
    Lib.showModal('login');
    e.preventDefault();
}, Lib);

JS;
		$this->view->getHelper('jQuery')->addOnLoad($js);
		$this->_hasRenderedActionLinkJS = true;
	}
	
	protected function _facebookLink($url, $title, $id = "", $class = "")
	{
		$linkTitle = ucfirst(sprintf($this->view->translate('facebookConnectLinkTitle'), APP_NAME));
		$fbLink = "<a$class$id title=\"$linkTitle\" href=\"".$url.'">'.ucfirst($title).'</a>'.PHP_EOL;
		return $fbLink;
	}

	public function loginRegistrationMarkup(User_Row $user, $showForm = false, $showLoginButton = true, $destinationAfterLogin = '', $fbLink = '')
	{
		$return = '';
		if(!$user->canLogin()){
			return $return;
		}

		$form = new User_Form_Login(array());
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$data = $request->getPost();
		if ($data){
			$showLoginButton = false;
			if($form->isValid($data)){
				// Form is valid, we must now log in
				$request->setControllerName('user');
				$request->setActionName('login');
				$request->setDispatched(false);
			} else {
				$showForm = true;
			}
		}

		$return .= '<div class="connectionLinks">'.PHP_EOL;

		$loginLinkTitle = ucfirst(sprintf($this->view->translate('loginLinkTitle'), APP_NAME));
		if($destinationAfterLogin){
			$url = Globals::getRouter()->assemble(array('url' => $destinationAfterLogin), 'savedestinationforredirect', true, false);
			$form->setAction($url);
			$return .= '<a id="revealLoginButton" title="'.$loginLinkTitle.'" href="'.$url.'">'.ucfirst($this->view->translate('loginpage')).'</a>'.PHP_EOL;
		} else {
			$return .= $this->view->routeLink('loginpage', null, array(), '', 'revealLoginButton', $loginLinkTitle).PHP_EOL;
		}


		if(is_string($fbLink)){
			$return .= $fbLink.PHP_EOL;
		} elseif($fbLink === true){
			$id = " id=\"facebookConnect\"";
			$class = "";
			$oauth = new Facebook_Oauth(array(
				'scope' => array('email'),
				'destination' => APP_URL.$destinationAfterLogin,
			));
			$return .= $this->_facebookLink($oauth->buildInitialUrl(), $this->view->translate('fbConnect'), $id, $class);
		}
		$registerLinkTitle = ucfirst(sprintf($this->view->translate('registerLinkTitle'), APP_NAME));
		$return .= $this->view->routeLink('userregister', null, array(), '', 'registerButton', $registerLinkTitle).PHP_EOL;
		$return .= '</div>'.PHP_EOL;

		$return .= '<div class="loginStatus">'.PHP_EOL.$form.PHP_EOL;
		$return .= '	<p class="recoverPassword">'.$this->view->routeLink('lostpassword').'</p>'.PHP_EOL;
		$return .= '</div>'.PHP_EOL;

		$js = '';

		if(!$showForm){
			$js .= <<<JS

	$('div.loginStatus').hide();
JS;
		}

		if(!$showLoginButton){
			$js .= <<<JS

	$('#revealLoginButton').hide();
JS;

		}

		$js .= <<<JS

	$('#revealLoginButton').click(function(){
		$('div.connectionLinks').hide();
		$('div.loginStatus').fadeIn();
		return false;
	});
JS;

		$this->view->getHelper('jQuery')->addOnLoad($js);
		return $return;
	}
}