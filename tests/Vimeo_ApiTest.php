<?php
require_once('ApplicationTest.php');

class Vimeo_ApiTest extends ApplicationTest
{
	public function testVideo()
	{
		$videoId = 'abc123';
		$token = '321';
		$responseJson = "{'response': 'ok'}";
		$responseObj = Zend_Json::decode($responseJson);
	
		$uri = 'https://api.vimeo.com/videos';
		$uri .= '/'.$videoId;
		$httpClient = $this->_mockClient($uri, $responseJson);
	
		$apiClient = new Vimeo_Api($token, $httpClient);
	
		$this->assertEquals(
			$apiClient->getVideoInfo($videoId),
			$responseObj
		);
	}
	protected function _mockClient($uri, $responseContent)
	{
		$response = $this->createMock(
				'Zend_Http_Response', array(), array(), '', false);
		$response
			->expects($this->once())
			->method('isError')
			->will($this->returnValue(false));
		$response
			->expects($this->once())
			->method('getBody')
			->will($this->returnValue($responseContent));
	
		$client = $this->createMock('Zend_Http_Client');
		$client
			->expects($this->once())
			->method('setUri')
			->with($uri);
		$client
			->expects($this->once())
			->method('request')
			->will($this->returnValue($response));
		return $client;
	}
}