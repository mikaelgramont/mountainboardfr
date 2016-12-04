<?php
class Google_Api
{
	const BASE_URI = 'https://www.googleapis.com/';
	
	protected $_key;
	
	public function __construct($key, $client)
	{
		$this->_key = $key;
		$this->_client = $client;
	}
	
	protected function _makeRequest($resource, $params)
	{
		$params[] = 'key='.$this->_key;
		
		$fullUri = self::BASE_URI . $resource . '?' . implode($params, '&');
		$this->_client->setUri($fullUri);
		$response = $this->_client->request();
		if($response->isError()){throw new Google_Exception(
			"Could not get response for: '".$fullUri."' ".$response->getBody());
		}
		
		$data = Zend_Json::decode($response->getBody());
		
		return $data;
	}	
}