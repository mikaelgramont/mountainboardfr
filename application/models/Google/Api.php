<?php
class Google_Api
{
	const BASE_URI = 'https://www.googleapis.com/';
	
	const YOUTUBE_PATH = 'youtube/v3/';
	const YOUTUBE_VIDEO_RESOURCE = 'videos';
	
	protected $_key;
	
	public function __construct($key, $client)
	{
		$this->_key = $key;
		$this->_client = $client;
	}
	
	public function getYouTubeVideoInfo($id)
	{
		$params = array();
		$params[] = 'id='.$id;
		$params[] = 'part=snippet';
		
		$data = $this->_makeRequest(
				self::YOUTUBE_PATH . self::YOUTUBE_VIDEO_RESOURCE, $params);
		return $data;
	}
	
	private function _makeRequest($resource, $params)
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