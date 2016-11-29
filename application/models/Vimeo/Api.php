<?php
class Vimeo_Api
{
	const BASE_URI = 'https://api.vimeo.com/';
	
	const VIDEO_RESOURCE = 'videos';

	public function __construct($token, $client)
	{
		$this->_token = $token;
		$this->_client = $client;
	}
	
	public function getVideoInfo($id)
	{
		$data = $this->_makeRequest(self::VIDEO_RESOURCE . '/'. $id);
		return $data;
	}
	
	private function _makeRequest($resource)
	{
		$fullUri = self::BASE_URI.$resource;
		$this->_client->setUri($fullUri);
		$this->_client->setHeaders('Authorization', 'Bearer '.		$this->_token);
		$response = $this->_client->request();
		if($response->isError()){throw new Vimeo_Exception(
			"Could not get response for: '".$fullUri."' ".$response->getBody());
		}
		$data = Zend_Json::decode($response->getBody());
	
		return $data;
	}
}