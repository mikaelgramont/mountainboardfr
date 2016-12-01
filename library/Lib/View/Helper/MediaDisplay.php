<?php
class Lib_View_Helper_MediaDisplay extends Zend_View_Helper_Abstract
{
	const TARGET_VIDEO_SIZE = 800;

	protected $_flickrPhoto;

	public function mediaDisplay()
	{
		return $this;
	}

	public function render(Media_Item_Row $media, $editable = false)
	{
		switch($media->mediaType){
		    case Media_Item::TYPE_PHOTO:
		        $content = $this->photoDisplay($media, $editable);
		        break;
		    case Media_Item::TYPE_VIDEO:
		        $content = $this->videoDisplay($media);
		        break;
		    default:
		        throw new Lib_Exception("No renderer for media type: '{$media->mediaType}'");
		        break;
		}
        return $content;
	}

	public function photoDisplay(Media_Item_Row $media, $editable)
	{
		$width = $media->getWidth();
		$height = $media->getHeight();
		$description = $media->getDescription();
		$alt = strip_tags($description);
		$title = strip_tags($media->getTitle());

		switch($media->mediaSubType){
		    case Media_Item_Photo::SUBTYPE_JPG:
		    case Media_Item_Photo::SUBTYPE_PNG:
		    case Media_Item_Photo::SUBTYPE_GIF:
        		/**
        		 * @todo: ne mettre que les attributs que l'on connait
        		 * echapper les " dans les title et alt
        		 */
        		$src = $media->getURI();
        		$src = $this->view->cdnHelper->url($src);
		        break;

		    case Media_Item_Photo::SUBTYPE_FLICKR:
		    	$flickr = new Zend_Service_Flickr(FLICKR_API_KEY);
		    	$this->_flickrPhoto = $flickr->getImageDetails($media->uri);
		        if(empty($this->_flickrPhoto)){
		            throw new Lib_Exception("Photo not found on Flickr: '$media->uri'");
		        }
		        if(!array_key_exists(FLICKR_PHOTOS_SIZE, $this->_flickrPhoto)){
		        	throw new Lib_Exception("Size not found for photo '$media->uri': ".FLICKR_PHOTOS_SIZE);
		        }
		    	$src = $this->_flickrPhoto[FLICKR_PHOTOS_SIZE]->uri;
		    	$src = $this->view->cdnHelper->url($src);
				$width = $this->_flickrPhoto[FLICKR_PHOTOS_SIZE]->width;
				$height = $this->_flickrPhoto[FLICKR_PHOTOS_SIZE]->height;
		        break;

		    default:
		        throw new Lib_Exception("Unknown photo subtype: '{$media->mediaSubType}'");
		        break;
		}
		$content = "<a target=\"_blank\" class=\"media-wrapper photo-wrapper\" href=\"$src\"><img src = \"$src\" alt=\"$alt\" title=\"$title\" /></a>".PHP_EOL;
		
		return $content;
	}

	public function videoDisplay(Media_Item_Row $media)
	{
		$width = $media->getWidth();
		$height = $media->getHeight();

		if($width < self::TARGET_VIDEO_SIZE){
			$targetWidth = self::TARGET_VIDEO_SIZE;
			$targetHeight = floor($height * $targetWidth / $width);
		} else {
			$targetWidth = $width;
			$targetHeight = $height;
		}

		$title = strip_tags($media->getTitle());

		$id = $media->getCleanTitle().'_'.$media->id;
		$uri = $media->getURI();

		switch($media->mediaSubType){
			case Media_Item_Video::SUBTYPE_YOUTUBE:
			case Media_Item_Video::SUBTYPE_VIMEO:
			case Media_Item_Video::SUBTYPE_DAILYMOTION:
				$video = '<div class="media-wrapper video-wrapper">'.$media->getProviderCode().'</div>';
				break;
			default:
				throw new Lib_Exception_Media("Unsupported mediaSubType '$this->mediaSubType' for video '$this->id'");
				break;
		}
		return $video;
	}
	

	public function alternateLink(Media_Item_Row $media)
	{
		$return = "";

		switch($media->mediaType){
		    case Media_Item::TYPE_PHOTO:
		    	if($media->mediaSubType == Media_Item_Photo::SUBTYPE_FLICKR){
		    		if(empty($this->_flickrPhoto)){
		    			return $return;
		    		}
		    		$title = ucfirst($this->view->translate('seeOnFlickr'));
		    		$link = $this->_flickrPhoto[FLICKR_PHOTOS_SIZE]->clickUri;
		    		$return ="<a class=\"external flickr\" href=\"$link\" rel=\"alternate\">$title</a>".PHP_EOL;
		    	}
		        break;

			case Media_Item::TYPE_VIDEO:
				switch($media->mediaSubType){
			    case Media_Item_Video::SUBTYPE_YOUTUBE:
						$alternateTitle = 'seeOnYouTube';
						$class = " youtube";
				        break;
				    case Media_Item_Video::SUBTYPE_VIMEO:
				    	$alternateTitle = 'seeOnVimeo';
				    	$class = " vimeo";
				        break;
				    case Media_Item_Video::SUBTYPE_DAILYMOTION:
				    	$alternateTitle = 'seeOnDailyMotion';
				    	$class = " dailymotion";
				        break;
				}
				$alternate = $media->getExternalURI();
				$return = '<a class="external'.$class.'" href="'.$alternate.'" rel="alternate">'.ucfirst(Globals::getTranslate()->_($alternateTitle)).'</a>'.PHP_EOL;

		    	break;
		    default:
		        throw new Lib_Exception("No alternate link renderer for media type: '{$media->mediaType}'");
		        break;
		}

		return $return;
	}
}
