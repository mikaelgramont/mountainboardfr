<?php
class Media_Item_Photo_Row extends Media_Item_Row
{
	/**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = Constants_DataTypes::PHOTO;

    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'displayphoto';

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'editphoto';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'uploadphotomain';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deletephoto';

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Media_Item_Photo_Form';

    /**
     * Deletes photo files if they exist
     *
     */
    protected function _postDelete()
    {
        parent::_postDelete();

        if(in_array($this->thumbnailSubType, array(Media_Item_Photo::SUBTYPE_JPG, Media_Item_Photo::SUBTYPE_GIF, Media_Item_Photo::SUBTYPE_PNG))){
            try{
            	$path = $this->getThumbnailURI(false);
    			$thumbnail = new File($path);
            	$thumbnail->delete();
            	Globals::getLogger()->deletes("Deleted photo thumbnail file '$path'", Zend_Log::INFO);
    		} catch(Exception $e) {
    			 Globals::getLogger()->deletes("Could not find photo thumbnail file for deletion: '$path'", Zend_Log::INFO);
        	}
        }

        if(in_array($this->mediaSubType, array(Media_Item_Photo::SUBTYPE_JPG, Media_Item_Photo::SUBTYPE_GIF, Media_Item_Photo::SUBTYPE_PNG))){
            try{
            	$path = $this->getURI(false);
            	$file = new File($path);
            	$file->delete();
            	Globals::getLogger()->deletes("Deleted photo file '$path'", Zend_Log::INFO);
    		} catch(Exception $e) {
    			 Globals::getLogger()->deletes("Could not find photo file for deletion: '$path'", Zend_Log::INFO);
    		}
        }
    }

    public function getMediaType()
	{
		return Media_Item::TYPE_PHOTO;
	}

	public function getMediaSubType()
	{
		if(!in_array($this->mediaSubType, Media_Item_Photo::$allowedMediaSubTypes)){
			throw new Lib_Exception_Media("Bad subtype: '$this->mediaSubType' for media '$this->id'");
		}
		return $this->mediaSubType;
	}

	public function getURI($absolute = true)
	{
		$url = APP_MEDIA_DIR.'/'.$this->uri;
		if($absolute){
			$url = APP_URL.'/'.$url;
		}
		return $url;
	}

	/**
	 * Performs a rotation of the photo and its thumbnail
	 *
	 * @param int $angle
	 */
	public function rotate($angle)
	{
		$filenamePrefix = $this->getCleanTitle().'_'.uniqid();

		$filename = $filenamePrefix.'.'.$this->mediaSubType;
		$photoFile = new File_Photo($this->getURI(false));
		$photoFile->rotate($angle, $this->getMediaSubType());
		$photoFile->rename($filename);
		$this->uri = $filename;

		$thumbnailFilename = $filenamePrefix.'.'.$this->thumbnailSubType;
		$thumbnail = new File_Photo($this->getThumbnailURI(false));
		$thumbnail->rotate($angle, $this->getThumbnailSubType());
		$thumbnail->rename($thumbnailFilename);
		$this->thumbnailUri = $thumbnailFilename;

		if($angle != 180){
			// Only update dimensions if we rotate 90 degrees
			$temp = $this->width;
			$this->width = $this->height;
			$this->height = $temp;

			$temp = $this->thumbnailWidth;
			$this->thumbnailWidth = $this->thumbnailHeight;
			$this->thumbnailHeight = $temp;
		}

		$this->save();
	}

    /**
     * Returns a form to upload a photo
     *
     * @param User_Row $user
     * @param Lib_Acl $acl
     * @param array $options
     * @return Media_Item_Photo_Form
     */
	public function getForm(User_Row $user, Lib_Acl $acl, $options = null)
    {
        $form = parent::getForm($user, $acl, $options);
        if($this->id){
        	// Edition form
        	$action = Globals::getRouter()->assemble(array('id' => $this->id), 'editphoto', true);
        } else {
        	// Creation form
	        switch($this->albumId){
	        	case Media_Album_PhotoMain::ID:
	        		$action = Globals::getRouter()->assemble(array(), 'uploadphotomain', true);
	        		break;
	        	case Media_Album_Portfolio::ID:
	        		$action = Globals::getRouter()->assemble(array(), 'uploadportfolio', true);
	        		break;
	        	case Media_Album_VideoMain::ID:
	        		throw new Lib_Exception_Media("Attempting to create photo form for video album '{$this->albumId}'");
	        		break;
				default:
					$albumTable = new Media_Album();
					$album = $albumTable->find($this->albumId)->current();
					if(empty($album)){
						throw new Lib_Exception_Media("Creating media form: album '{$this->albumId}' does not exist");
					}
	        		$action = Globals::getRouter()->assemble(array('albumId' => $this->albumId), 'uploadphoto', true);
	        		break;
	        }
        }
        $form->setAction($action);
        return $form;
    }
}