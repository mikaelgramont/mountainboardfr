<?php
class Google_Api_Youtube extends Google_Api
	implements VideoThumbnailFetcherInterface
{
	const YOUTUBE_PATH = 'youtube/v3/';
	const YOUTUBE_VIDEO_RESOURCE = 'videos';
	
	protected $_key;
	
	public function getVideoInfo($id)
	{
		$params = array();
		$params[] = 'id='.$id;
		$params[] = 'part=snippet';
	
		$data = $this->_makeRequest(
				self::YOUTUBE_PATH . self::YOUTUBE_VIDEO_RESOURCE, $params);
		return $data;
	}
	
	public function getThumbnailInfo($id, $size)
	{
		$ret = array();
		$videoData = $this->getVideoInfo($id);
		$thumbnail = @$videoData['items'][0]['snippet']['thumbnails']['high'];
		if (!$thumbnail) {
			throw new Google_Exception(sprintf(
				"No thumbnail found for YouTube video '%s'", $id));
		}
		$ret['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_YOUTUBE_THUMBNAIL;
		$ret['thumbnailUri'] = $thumbnail['url'];
		$ret['thumbnailWidth'] = $thumbnail['width'];
		$ret['thumbnailHeight'] = $thumbnail['height'];
		return $ret;
	}	
}