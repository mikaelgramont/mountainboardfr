<?php
class Dailymotion_Api
{
	const BASE_URI = 'https://api.dailymotion.com/';

	const VIDEO_RESOURCE = 'video';

	public function __construct( $client)
	{
		$this->_client = $client;
	}

	public function getVideoInfo($id)
	{
		$data = $this->_makeRequest(self::VIDEO_RESOURCE.'/'.$id);
		return $data;
	}

	private function _makeRequest($resource)
	{
		$fullUri = self::BASE_URI . $resource;
		$fullUri .= "?fields=thumbnail_480_url";
		$this->_client->setUri($fullUri);
		$response = $this->_client->request();
		if($response->isError()){throw new Dailymotion_Exception(
			"Could not get response for: '".$fullUri."' ".$response->getBody());
		}

		$data = Zend_Json::decode($response->getBody());

		return $data;
	}

}