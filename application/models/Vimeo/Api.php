<?php
class Vimeo_Api implements VideoThumbnailFetcherInterface
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
	
	public function getThumbnailInfo($id, $size)
	{
		$ret = array();
		$videoData = $this->getVideoInfo($id);
		$ret['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_VIMEO_THUMBNAIL;
		$thumbnail = $videoData['pictures'][0];
		$ret['thumbnailUri'] = $thumbnail['link'];
		$ret['thumbnailWidth'] = $thumbnail['width'];
		$ret['thumbnailHeight'] = $thumbnail['height'];
		return $ret;
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