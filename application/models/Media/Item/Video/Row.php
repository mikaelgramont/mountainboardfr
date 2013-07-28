<?php
class Media_Item_Video_Row extends Media_Item_Row
{
	/**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = Constants_DataTypes::VIDEO;

    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'displayvideo';
        
    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'editvideo';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'uploadvideo';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deletevideo';
        
    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Media_Item_Video_Form';

    protected function _postDelete()
    {
        parent::_postDelete();

        if(in_array($this->thumbnailSubType, array(Media_Item_Photo::SUBTYPE_JPG, Media_Item_Photo::SUBTYPE_GIF, Media_Item_Photo::SUBTYPE_PNG))){
            try{
            	$path = $this->getThumbnailURI(false);
            	$thumbnail = new File($path);
            	$thumbnail->delete();
            	Globals::getLogger()->deletes("Deleted video thumbnail file '$path'", Zend_Log::INFO);
    		} catch(Exception $e) {
    			 Globals::getLogger()->deletes("Could not find video thumbnail file for deletion: '$path'", Zend_Log::INFO);
    		}
        }
    }

	public function getMediaSubType()
	{
		if(!in_array($this->mediaSubType, Media_Item_Video::$allowedMediaSubTypes)){
			throw new Lib_Exception_Media("Bad subtype: '$this->mediaSubType' for media '$this->id'");
		}
		return $this->mediaSubType;
	}

    public function getMediaType()
	{
		return Media_Item::TYPE_VIDEO;
	}

	public function getURI()
	{
		$uri = '';
		switch($this->mediaSubType){
			case Media_Item_Video::SUBTYPE_YOUTUBE:
				$uri = 'http://www.youtube.com/v/'.$this->uri.'&fs=1';
				break;
			case Media_Item_Video::SUBTYPE_VIMEO:
				$uri = 'http://vimeo.com/moogaloop.swf?clip_id='.$this->uri.'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ffd500&amp;fullscreen=1';
				break;
			case Media_Item_Video::SUBTYPE_DAILYMOTION:
				$uri = 'http://www.dailymotion.com/swf/video/'.$this->uri;
				break;
			default:
				throw new Lib_Exception_Media("Unsupported mediaSubType '$this->mediaSubType' for video '$this->id'");
				break;
		}

		return $uri;
	}
	
	/**
	 * Returns the code that would have been provided by a video provider
	 * to be pasted somewhere
	 *
	 * @return string
	 */
	public function getProviderCode()
	{
		$code = '';
		switch($this->mediaSubType){
			case Media_Item_Video::SUBTYPE_YOUTUBE:
				$code = <<<CODE
<object width="{$this->width}" height="{$this->height}"><param name="movie" value="http://www.youtube.com/v/{$this->uri}&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/{$this->uri}&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="{$this->width}" height="{$this->height}"></embed></object>
CODE;
				break;
			case Media_Item_Video::SUBTYPE_VIMEO:
				$code = <<<CODE
<object width="{$this->width}" height="{$this->height}"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id={$this->uri}&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=00ADEF&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id={$this->uri}&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=00ADEF&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{$this->width}" height="{$this->height}"></embed></object>				
CODE;
				break;
			case Media_Item_Video::SUBTYPE_DAILYMOTION:
				$code = <<<CODE
<object width="{$this->width}" height="{$this->height}" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><param name="movie" value="http://www.dailymotion.com/swf/video/{$this->uri}&related=0"></param><param name="allowFullScreen" value="true"></param><param name="allowScriptAccess" value="always"></param><embed src="http://www.dailymotion.com/swf/{$this->uri}&related=0" type="application/x-shockwave-flash" width="{$this->width}" height="{$this->height}" allowFullScreen="true" allowScriptAccess="always"></embed></object>
CODE;
				break;
			default:
				throw new Lib_Exception_Media("Unsupported mediaSubType '$this->mediaSubType' for video '$this->id'");
				break;
		}

		return $code;
	}

	public function getExternalURI()
	{
		$uri = '';
		switch($this->mediaSubType){
			case Media_Item_Video::SUBTYPE_YOUTUBE:
				$uri = 'http://www.youtube.com/?v='.$this->uri;
				break;
			case Media_Item_Video::SUBTYPE_VIMEO:
				$uri = 'http://vimeo.com/'.$this->uri;
				break;
			case Media_Item_Video::SUBTYPE_DAILYMOTION:
				$uri = 'http://www.dailymotion.com/video/'.$this->uri;
				break;
			default:
				throw new Lib_Exception_Media("Unsupported mediaSubType '$this->mediaSubType' for video '$this->id'");
				break;
		}

		return $uri;

	}

    /**
     * Returns a form to upload a video
     *
     * @param User_Row $user
     * @param Lib_Acl $acl
     * @param array $options
     * @return Media_Item_Video_Form
     */
	public function getForm(User_Row $user, Lib_Acl $acl, $options = null)
    {
        $form = parent::getForm($user, $acl, $options);
        if($this->id){
        	// Edition form
        	$action = Globals::getRouter()->assemble(array('id' => $this->id), 'editvideo', true);
        } else {
        	$action = Globals::getRouter()->assemble(array(), 'uploadvideo', true);
        }
        $form->setAction($action);
        return $form;
    }
}