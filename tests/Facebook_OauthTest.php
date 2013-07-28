<?php
require_once('ApplicationTest.php');

class Facebook_OauthTest extends ApplicationTest
{
	public function setUp()
	{
		parent::setUp();
		Facebook_Oauth::clear();
	}

	public function testExceptionIfNoDestination()
	{
		$this->setExpectedException('Lib_Exception_Facebook');
		$oauth = new Facebook_Oauth(array());
	}

	public function testInitialUrlDefault()
	{
		$oauth = new Facebook_Oauth(array(
			'scope' => array('email','read_stream'),
			'destination' => 'dummy'
		));

		$expectedRedirectUrl = 'http://test.redesign-zend.fr/facebook/oauth';
		$this->assertEquals(
			$expectedRedirectUrl,
			$oauth->getRedirectUrl()
		);

		$expectedInitialUrl  = 'https://www.facebook.com/dialog/oauth?client_id='.FACEBOOK_APP_ID.'&redirect_uri=';
		$expectedInitialUrl .= urlencode($expectedRedirectUrl);
		$expectedInitialUrl .= '&scope=email%2Cread_stream';

		$this->assertEquals(
			$expectedInitialUrl,
			$oauth->buildInitialUrl()
		);

		$this->assertEquals(
			'dummy',
			$oauth->getDestination()
		);
	}

	public function testInitialUrlCreateAccount()
	{
		$oauth = new Facebook_Oauth(array(
			'scope' => array('email','read_stream'),
			'destination' => 'dummy',
			'type' => 'createAccount'
		));

		$expectedRedirectUrl = 'http://test.redesign-zend.fr/facebook/oauth/createAccount';
		$this->assertEquals(
			$expectedRedirectUrl,
			$oauth->getRedirectUrl()
		);
	}

	public function testRetrieveAccessTokenSuccess()
	{
		$oauth = new Facebook_Oauth(array(
			'scope' => array('email','read_stream'),
			'destination' => 'dummy',
			'redirectUrl' => 'http://tata'
		));

		$code = '123';

		$expectedUri  = 'https://graph.facebook.com/oauth/access_token?client_id='.FACEBOOK_APP_ID;
		$expectedUri .= '&redirect_uri=http%3A%2F%2Ftata&client_secret='.FACEBOOK_APP_SECRET.'&code='.$code;
		$expectedUriObj = Lib_Uri_Http::fromString($expectedUri);

		$successfulResponseBody = 'bla';
		$response = $this->getMock('Zend_Http_Response', array(), array(), '', false);
		$response->expects($this->once())
			     ->method('isError')
			     ->will($this->returnValue(false));
		$response->expects($this->once())
			     ->method('getBody')
			     ->will($this->returnValue($successfulResponseBody));



		$client = $this->getMock('Zend_Http_Client');
		$client->expects($this->once())
			   ->method('setUri')
			   ->with($expectedUriObj);
		$client->expects($this->once())
			   ->method('request')
			   ->will($this->returnValue($response));

		$accessToken = $oauth->retrieveAccessToken($client, $code);

		$this->assertEquals(
			'bla',
			$accessToken
		);
	}

	public function testRetrieveAccessTokenFailure()
	{
		$oauth = new Facebook_Oauth(array(
			'scope' => array('email','read_stream'),
			'destination' => 'dummy',
			'redirectUrl' => 'http://tata'
		));

		$code = '123';

		$expectedUri  = 'https://graph.facebook.com/oauth/access_token?client_id='.FACEBOOK_APP_ID;
		$expectedUri .= '&redirect_uri=http%3A%2F%2Ftata&client_secret='.FACEBOOK_APP_SECRET.'&code='.$code;
		$expectedUriObj = Lib_Uri_Http::fromString($expectedUri);

		$response = $this->getMock('Zend_Http_Response', array(), array(), '', false);
		$response->expects($this->once())
			     ->method('isError')
			     ->will($this->returnValue(true));
		$response->expects($this->never())
			     ->method('getBody');

		$client = $this->getMock('Zend_Http_Client');
		$client->expects($this->once())
			   ->method('setUri')
			   ->with($expectedUriObj);
		$client->expects($this->once())
			   ->method('request')
			   ->will($this->returnValue($response));

		$this->setExpectedException('Lib_Exception_Facebook');
		$accessToken = $oauth->retrieveAccessToken($client, $code);
	}

	public function testRetrieveDataSuccess()
	{
		$oauth = new Facebook_Oauth(array(
			'scope' => array('email','read_stream'),
			'destination' => 'dummy',
		));

		$accessToken = 'myaccesstoken';

		$expectedUri  = 'https://graph.facebook.com/me?'.$accessToken;
		$expectedUriObj = Lib_Uri_Http::fromString($expectedUri);

		$successfulResponseBody = '{"id":"123"}';
		$expectedData = Zend_Json::decode($successfulResponseBody);

		$response = $this->getMock('Zend_Http_Response', array(), array(), '', false);
		$response->expects($this->once())
			     ->method('isError')
			     ->will($this->returnValue(false));
		$response->expects($this->once())
			     ->method('getBody')
			     ->will($this->returnValue($successfulResponseBody));

		$client = $this->getMock('Zend_Http_Client');
		$client->expects($this->once())
			   ->method('setUri')
			   ->with($expectedUriObj);
		$client->expects($this->once())
			   ->method('request')
			   ->will($this->returnValue($response));

		$data = $oauth->retrieveData($client, $accessToken);

		$this->assertEquals($expectedData, $data);
	}

	public function testRetrieveDataFailure()
	{
		$oauth = new Facebook_Oauth(array(
			'scope' => array('email','read_stream'),
			'destination' => 'dummy',
		));

		$accessToken = 'myaccesstoken';

		$expectedUri  = 'https://graph.facebook.com/me?'.$accessToken;
		$expectedUriObj = Lib_Uri_Http::fromString($expectedUri);

		$successfulResponseBody = '{data:"ok"}';
		$expectedData = Zend_Json::decode($successfulResponseBody);

		$response = $this->getMock('Zend_Http_Response', array(), array(), '', false);
		$response->expects($this->once())
			     ->method('isError')
			     ->will($this->returnValue(true));
		$response->expects($this->never())
			     ->method('getBody')
			     ->will($this->returnValue($successfulResponseBody));

		$client = $this->getMock('Zend_Http_Client');
		$client->expects($this->once())
			   ->method('setUri')
			   ->with($expectedUriObj);
		$client->expects($this->once())
			   ->method('request')
			   ->will($this->returnValue($response));

		$this->setExpectedException('Lib_Exception_Facebook');
		$data = $oauth->retrieveData($client, $accessToken);
	}
}