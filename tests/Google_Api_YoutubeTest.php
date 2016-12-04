<?php
require_once('ApplicationTest.php');

class Google_Api_YoutubeTest extends ApplicationTest
{
	public function testVideo()
	{
		$apiKey = 'somekey';
		$videoId = 'abc123';
		$responseJson = "{'response': 'ok'}";
		$responseObj = Zend_Json::decode($responseJson);
		
		$uri = 'https://www.googleapis.com/youtube/v3/videos';
		$uri .= '?id='.$videoId;
		$uri .= '&part=snippet';
		$uri .= '&key='.$apiKey;
		$httpClient = $this->_mockClient($uri, $responseJson);
		
		$apiClient = new Google_Api_Youtube($apiKey, $httpClient);
		
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