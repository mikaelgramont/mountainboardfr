<?php
class Dailymotion_Api implements VideoThumbnailFetcherInterface
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

	public function getThumbnailInfo($id, $size)
	{
		$ret = array();
		$videoData = $this->getVideoInfo($id);
		$ret['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_DAILYMOTION_THUMBNAIL;
		$ret['thumbnailUri'] = $videoData['thumbnail_480_url'];
		$ret['thumbnailWidth'] = 640;
		$ret['thumbnailHeight'] = 480;
		return $ret;
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