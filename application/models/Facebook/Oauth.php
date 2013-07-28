<?php
class Facebook_Oauth
{
	const OAUTH_URL = 'https://www.facebook.com/dialog/oauth';
	const GRAPH_URL = 'https://graph.facebook.com';

	const SESSION_NAMESPACE = 'facebookOauth';

	protected $_options = array(
		'scope' => array(),
		'redirectUrl' => '',
		'type' => '', // is this necessary if we redirect to destination?
		'destination' => '',
	);

	public static function clear()
	{
		$namespace = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
		$namespace->unsetAll();
	}

	public function __construct(array $customOptions = array())
	{
		$this->_namespace = new Zend_Session_Namespace(self::SESSION_NAMESPACE);

		$this->_options = array_merge($this->_options, $customOptions);

		if(empty($this->_options['destination']) && !isset($this->_namespace->destination)){
			throw new Lib_Exception_Facebook('No destination defined', Lib_Exception_Facebook::OAUTH_SETTINGS_WRONG_OR_INCOMPLETE);
		}

		if(empty($this->_options['redirectUrl']) && !($this->_namespace->redirectUrl)){
			$this->_options['redirectUrl'] = APP_URL.Globals::getRouter()->assemble(
				array('type' => $this->_options['type']),
				'facebookoauth',
				true
			);
		}
		if(!empty($this->_options['redirectUrl'])){
			$this->_namespace->redirectUrl = $this->_options['redirectUrl'];
		}

		if(!empty($this->_options['destination'])){
			$this->_namespace->destination = $this->_options['destination'];
		}
	}

	// GETTERS

	public function getRedirectUrl()
	{
		if(!isset($this->_namespace->redirectUrl)){
			throw new Lib_Exception_Facebook('No redirect url found', Lib_Exception_Facebook::OAUTH_SETTINGS_WRONG_OR_INCOMPLETE);
		}
		$return = $this->_namespace->redirectUrl;
		return $return;
	}

	public function getDestination()
	{
		if(!isset($this->_namespace->destination)){
			throw new Lib_Exception_Facebook('No destination found', Lib_Exception_Facebook::OAUTH_SETTINGS_WRONG_OR_INCOMPLETE);
		}
		$destination = $this->_namespace->destination;
		return $destination;
	}

	public function getData()
	{
		if(!isset($this->_namespace->data)){
			throw new Lib_Exception_Facebook('No data found', Lib_Exception_Facebook::FACEBOOK_DATA_WRONG_OR_INCOMPLETE);
		}
		$data = $this->_namespace->data;
		return $data;
	}

	// METHODS

	public function buildInitialUrl()
	{
		$scope = implode(',', $this->_options['scope']);

		$url = self::OAUTH_URL.
			   '?client_id='.FACEBOOK_APP_ID.
			   '&redirect_uri='.urlencode($this->getRedirectUrl());

		if(!empty($scope)){
			$url .= '&scope='.urlencode($scope);
		}

		return $url;
	}

	public function retrieveAccessToken(Zend_Http_Client $client, $code)
	{
		$uri = self::GRAPH_URL.'/oauth/access_token'.
			   '?client_id='.FACEBOOK_APP_ID.
			   '&redirect_uri='.urlencode($this->getRedirectUrl()).
			   '&client_secret='.FACEBOOK_APP_SECRET.
			   '&code='.$code;

		$uriObj = Lib_Uri_Http::fromString($uri);
		$client->setUri($uriObj);

		$response = $client->request();

		if($response->isError()){
			throw new Lib_Exception_Facebook('Could not get access token', Lib_Exception_Facebook::FACEBOOK_DATA_WRONG_OR_INCOMPLETE);
		}

		$accessToken = $response->getBody();
		return $accessToken;
	}

	public function retrieveData(Zend_Http_Client $client, $accessToken)
	{
		$uri = self::GRAPH_URL.'/me?'.$accessToken;
		$uriObj = Lib_Uri_Http::fromString($uri);
		$client->setUri($uriObj);
		$response = $client->request();

		if($response->isError()){
			throw new Lib_Exception_Facebook('Could not get retrieve data', Lib_Exception_Facebook::FACEBOOK_DATA_WRONG_OR_INCOMPLETE);
		}

		$json = $response->getBody();

		$data = Zend_Json::decode($json);

		if(!isset($data['id'])){
			throw new Lib_Exception_Facebook('Did not find facebook user id', Lib_Exception_Facebook::FACEBOOK_DATA_WRONG_OR_INCOMPLETE);
		}

		$this->_namespace->data = $data;

		return $data;
	}
}