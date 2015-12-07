<?php
class Lib_View_Helper_MediaThumbnail extends Zend_View_Helper_Abstract
{
	protected $_showText = false;
	protected $_showCommentsLink = false;

	public function mediaThumbnail($rawMedia, $showText = false, $showCommentsLink = false)
	{
		$this->_showText = $showText;
		$this->_showCommentsLink = $showCommentsLink;

		if($rawMedia instanceof Media_Item_Row){
			$media = $rawMedia;
		} elseif(isset($rawMedia['id']) || isset($rawMedia['mediaType'])) {
			$media = Media_Item_Factory::buildItem($rawMedia['id'], $rawMedia['mediaType']);
		} else {
			throw new Lib_Exception('No id or type defined for media thumbnail display');
		}

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

		return $content;
	}

	protected function _photoThumbnail(Media_Item_Photo_Row $media)
	{
		$src = $this->view->baseUrl .'/'. $media->getThumbnailURI(false);
		$width = $media->getThumbnailWidth();
		$height = $media->getThumbnailHeight();
		$description = $this->view->escape(strip_tags($media->getDescription()));
		$title = ucfirst(strip_tags($media->getTitle()));

		$viewsText = '';

		/**
         * @todo: ne mettre que les attributs que l'on connait
    	 * echapper les " dans les title et alt
         */

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

		$alt = '';
		$src = $this->view->cdnHelper->url($src);
		$img = "<img class=\"media photo\" src = \"$src\" width=\"$width\" height=\"$height\" alt=\"$alt\" title=\"$title $viewsText\" />";
		$link = $media->getLink();
		$content  = $this->_mediaLink($link, $img);

		if($this->_showText){
			$content .= "<a class=\"mediaThumbnailTitle dataLink photo\" href=\"$link\">$title</a>".PHP_EOL;
		}

		if($this->_showCommentsLink){
			$content .= $this->_mediaComments($media);
		}
		return $content;
	}

	protected function _videoThumbnail(Media_Item_Video_Row $media)
	{
		$width = $media->getThumbnailWidth();
		$height = $media->getThumbnailHeight();
		$description = $this->view->escape($media->getDescription());
		$title = ucfirst($media->getTitle());

		$viewsText = '';

		switch($media->thumbnailSubType){
		    case Media_Item_Photo::SUBTYPE_JPG:
				$src = $media->getThumbnailURI(false);
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
		        throw new Lib_Exception("Unknown media thumbnail subtype: '{$media->thumbnailSubType}'");
		        break;
		}

		$alt = "";
		$src = $this->view->baseUrl .'/'. $this->view->cdnHelper->url($src);
		$img = "<img src = \"$src\" width=\"$width\" height=\"$height\" alt=\"$alt\" title=\"$title $viewsText\" class=\"media video\"/>";
		$link = $media->getLink();
		$content = $this->_mediaLink($link, $img);

		if($this->_showText){
			$content .= "<a class=\"mediaThumbnailTitle dataLink video\" href=\"$link\">$title</a>".PHP_EOL;
		}

		if($this->_showCommentsLink){
			$content .= $this->_mediaComments($media);
		}

		return $content;
	}

	protected function _mediaLink($link, $innerHTML)
	{
		$content = "<a href=\"$link\" class=\"mediaLinkThumbnail\">$innerHTML</a>";
		return $content;
	}

	protected function _mediaComments(Media_Item_Row $media)
	{
		$content = '';
		$comments = $media->getComments($this->view->user, $this->view->acl);
		if($nbComments = count($comments)){
			$firstComment = $comments->rewind()->current();
			$commentLink = $firstComment->getLink();
			$nbCommentsString = $nbComments . ' '.$this->view->translate($nbComments > 1 ? 'itemPlur_'.Constants_DataTypes::COMMENT : 'itemSing_'.Constants_DataTypes::COMMENT);
			$content = '<a class="mediaThumbnailComments" href="'.$commentLink.'">'.$nbCommentsString.' </a>'.PHP_EOL;
		}
		return $content;

	}
}
