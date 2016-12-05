<?php
class Lib_View_Helper_MediaThumbnail extends Zend_View_Helper_Abstract
{
	protected $_showText = false;
	protected $_showCommentsLink = false;

	public function mediaThumbnail($rawMedia, $showText = false, 
		$showCommentsLink = false, $renderAsBackground = false)
	{
		$this->_showText = $showText;
		$this->_showCommentsLink = $showCommentsLink;
		$this->_renderAsBackground = $renderAsBackground;
		$media = $this->_getMediaMaybeFromRawObject($rawMedia);

		$type = $media->getMediaType();
		switch($type){
			case Media_Item::TYPE_PHOTO:
				$content = $this->_photoThumbnail($media);
				break;
			case Media_Item::TYPE_VIDEO:
				$content = $this->_videoThumbnail($media);
				break;
			default:
				throw new Lib_Exception("Unknow media type: '$type'");
				break;
		}
		$title = ucfirst(strip_tags($media->getTitle()));
		$commentsCount = $this->_getMediaCommentsCount($media);
		$content .= $this->_renderOverlay($title, $media->getMediaType(),
			$commentsCount);
		
		return $content;
	}
	
	protected function _backgroundUrl($rawMedia)
	{
		return 'someUrl';
	}

	protected function _getMediaMaybeFromRawObject($rawMedia)
	{
		if($rawMedia instanceof Media_Item_Row){
			$media = $rawMedia;
		} elseif(isset($rawMedia['id']) || isset($rawMedia['mediaType'])) {
			$media = Media_Item_Factory::buildItem($rawMedia['id'], $rawMedia['mediaType']);
		} else {
			throw new Lib_Exception('No id or type defined for media thumbnail display');
		}
		return $media;
	}
	
	protected function _photoThumbnail(Media_Item_Photo_Row $media)
	{
		$width = $media->getThumbnailWidth();
		$height = $media->getThumbnailHeight();
		$description = $this->view->escape(strip_tags($media->getDescription()));
		$title = ucfirst(strip_tags($media->getTitle()));

		switch($media->thumbnailSubType){
		    case Media_Item_Photo::SUBTYPE_JPG:
		    case Media_Item_Photo::SUBTYPE_PNG:
		    case Media_Item_Photo::SUBTYPE_GIF:
		        break;

		    case Media_Item_Photo::SUBTYPE_FLICKR:
		        $f = new phpFlickr(FLICKR_API_KEY);
		        $photo = $f->photos_getInfo($media->uri, FLICKR_API_SECRET);
		        if(empty($photo)){
		            throw new Lib_Exception("Photo not found on Flickr: '$media->uri'");
		        }
                $src = $f->buildPhotoURL($photo, FLICKR_THUMBNAILS_SIZE);

		        break;

		    default:
		        throw new Lib_Exception("Unknown media thumbnail subtype: '{$media->thumbnailSubType}'");
		        break;
		}

		$size = $this->_renderAsBackground ? Media_Item_Row::SIZE_MEDIUM : null;
		$src = $this->view->baseUrl .'/'. $media->getThumbnailURI(false, $size);
		$content = $this->_renderThumbnail('photo', $src, $media, $title, $width, $height);
		return $content;
	}
	
	protected function _renderThumbnail($class, $src, $media, $title, $width,
		$height)
	{
		$src = $this->view->cdnHelper->imgUrl($src);
		$link = $media->getLink();
		if ($this->_renderAsBackground) {
			$content = "<a class=\"mediaLinkThumbnail\" href=\"$link\" style=\"background-image: url($src)\" aria-label=\"$title\"></a>".PHP_EOL;
		} else {
			$alt = '';
			$img = "<img class=\"media photo\" src = \"$src\" width=\"$width\" height=\"$height\" alt=\"$alt\" title=\"$title\" />";
			$content  = $this->_mediaLink($link, $img);
		
			if($this->_showText){
				$content .= "<a class=\"mediaThumbnailTitle dataLink $class\" href=\"$link\">$title</a>".PHP_EOL;
			}
		}
		return $content;
	}

	protected function _renderOverlay($title, $type, $commentsCount)
	{
		$ret = "<div class=\"mediaOverlay\">".PHP_EOL;
		$ret .= "	<a class=\"mediaOverlayIcon dataLink $type\"></a>".PHP_EOL;
		$ret .= "	<span class=\"mediaOverlayTitle\">$title</span>".PHP_EOL;
		if ($commentsCount > 0) {
			$ret .= "	<span class=\"dataLink mediaOverlayComments\">$commentsCount</span>".PHP_EOL;
		}
		$ret .= "</div>".PHP_EOL;
		return $ret;
	}
	
	protected function _videoThumbnail(Media_Item_Video_Row $media)
	{
		$width = $media->getThumbnailWidth();
		$height = $media->getThumbnailHeight();
		$description = $this->view->escape($media->getDescription());
		$title = ucfirst($media->getTitle());

		$viewsText = '';
		$size = $this->_renderAsBackground ? Media_Item_Row::SIZE_MEDIUM : null;
		
		switch($media->thumbnailSubType){
		    case Media_Item_Photo::SUBTYPE_JPG:
				$src = $this->view->baseUrl .'/'. $media->getThumbnailURI(
					false, $size);
		        break;

		    case Media_Item_Photo::SUBTYPE_VIMEO_THUMBNAIL:
		    	$src = $media->thumbnailUri;
		        break;

		    case Media_Item_Photo::SUBTYPE_DAILYMOTION_THUMBNAIL:
		    	$src = $media->thumbnailUri;
		        break;

		    case Media_Item_Photo::SUBTYPE_YOUTUBE_THUMBNAIL:
		    	$src = $media->thumbnailUri;
		        break;

			case NULL:
				$src = $media->getThumbnailURI(false);
		    	break;

		    default:
		        throw new Lib_Exception(
		        	"Unknown media thumbnail subtype: '".
		        	"{$media->thumbnailSubType}'"
		        );
		        break;
		}
		$content = $this->_renderThumbnail('video', $src, $media, $title,
			$width, $height);
		return $content;
	}

	protected function _mediaLink($link, $innerHTML)
	{
		$content = "<a href=\"$link\" class=\"mediaLinkThumbnail\">$innerHTML</a>";
		return $content;
	}

	protected function _getMediaCommentsCount(Media_Item_Row $media)
	{
		$comments = $media->getComments($this->view->user, $this->view->acl);
		return count($comments);
	}
}
