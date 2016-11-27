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
				$uri = 'https://www.youtube.com/embed/'.$this->uri;
				break;
			case Media_Item_Video::SUBTYPE_VIMEO:
				$uri = 'https://player.vimeo.com/video/'.$this->uri;
				break;
			case Media_Item_Video::SUBTYPE_DAILYMOTION:
				$uri = 'https://www.dailymotion.com/embed/video/'.$this->uri;
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
<iframe width="{$this->width}" height="{$this->height}" src="https://www.youtube.com/embed/{$this->uri}" frameborder="0" allowfullscreen></iframe>
CODE;
				break;
			case Media_Item_Video::SUBTYPE_VIMEO:
				$code = <<<CODE
<iframe src="https://player.vimeo.com/video/{$this->uri}" width="{$this->width}" height="{$this->height}" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
CODE;
				break;
			case Media_Item_Video::SUBTYPE_DAILYMOTION:
				$code = <<<CODE
<iframe frameborder="0" width="{$this->width}" height="{$this->height}" src="//www.dailymotion.com/embed/video/{$this->uri}" allowfullscreen></iframe>				
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
				$uri = 'https://www.youtube.com/watch?v='.$this->uri;
				break;
			case Media_Item_Video::SUBTYPE_VIMEO:
				$uri = 'https://vimeo.com/'.$this->uri;
				break;
			case Media_Item_Video::SUBTYPE_DAILYMOTION:
				$uri = 'https://www.dailymotion.com/video/'.$this->uri;
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