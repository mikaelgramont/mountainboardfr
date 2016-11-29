<?php
class MediaController extends Lib_Controller_Action
{
    /**
     * List of fields in a form that will never match
     * a field in the data DB table.
     * Example: 'submit'
     *
     * @var array
     */
    protected $_disregardUpdates = array(
        'tags',
        'submit',
        'skipAutoFields',
        'longitude',
        'latitude',
        'zoom',
        'yaw',
        'pitch',
        'mapType',
        'token',
    	'media',
    	'riders',
    	'path',
    	'locationFlag'
    );

	/**
     * Initialisations
     */
    public function init()
    {
        parent::init();
        Zend_Registry::set('Category', Category::COMMUNITY);
    }

    /**
     * Display a media album in a dedicated page
     */
    public function displayalbumAction()
    {
    	$this->_useAdditionalContent = false;

    	$albumName = $this->_request->getParam('albumName');
		$albumId = $this->_request->getParam('albumId');
		$page = $this->_request->getParam('page');

		$album = $this->_getAlbumFromNameOrId($albumName, $albumId, $page);
    	if(empty($album)){
    		throw new Lib_Exception_NotFound("Album '$albumName' / '$albumId' could not be found");
    	}

    	if((!$album->isReadableBy($this->_user, $this->_acl))){
            $this->_helper->redirectToRoute('othererror', array('error'=>'unauthorizedPrivateAlbumRead'), true);
    	}

    	Zend_Registry::set('Category', $album->getCategory());
    	Zend_Registry::set('SubCategory', $album->getSubCategory());

        $comments = $album->getComments($this->_user, $this->_acl);

    	$this->view->album = $album;
    	$this->view->page = $page;
        $this->view->comments = $comments;
        if($identity = Zend_Auth::getInstance()->getIdentity()){
        	$this->view->lastLogin = $identity->lastLogin;
        } else {
        	$this->view->lastLogin = null;
        }

    	$view = $album->getViewScript();
    	$this->renderScript($view);
    }

	/**
	 * Display a media in a dedicated page
	 */
    public function displaymediaAction()
	{
    	$this->_useAdditionalContent = false;

		$id = $this->_request->getParam(3);

		$table = new Media_Item();
		$media = $table->find($id)->current();

		if(empty($media)){
			throw new Lib_Exception_NotFound("Media '$id' does not exist");
		}

		if(!$media->isReadableBy($this->_user, $this->_acl)){
            $this->_helper->redirectToRoute('othererror', array('error'=>'unauthorizedPrivateMediaRead'), true);
		}

		$media = Media_Item_Factory::buildItem($media->getId(), $media->getMediaType());
		$album = $media->getAlbum();
		$previousMedia = $album->getPreviousItem($media);
		$nextMedia = $album->getNextItem($media);
   		$location = $media->hasLocation() ? $media->getLocation() : null;
    	$comments = $media->getComments($this->_user, $this->_acl);
    	$riders = $media->getRidersInMedia();

    	$media->viewBy($this->_user, $this->getRequest());

    	Zend_Registry::set('Category', $album->getCategory());
    	Zend_Registry::set('SubCategory', $album->getSubCategory());

    	$this->view->media = $media;
		$this->view->album = $album;
		$this->view->previousMedia = $previousMedia;
		$this->view->nextMedia = $nextMedia;
		$this->view->spot = $media->hasSpot() ? $media->getSpot() : null;
		$this->view->trick = $media->hasTrick() ? $media->getTrick() : null;
		$this->view->location = $location;
		$this->view->comments = $comments;
		$this->view->riders = $riders;
        if($identity = Zend_Auth::getInstance()->getIdentity()){
        	$this->view->lastLogin = $identity->lastLogin;
        } else {
        	$this->view->lastLogin = null;
        }

        $this->view->isPrefetch = ($this->_request->getHeader('X-moz') == 'prefetch');

    	$view = $media->getViewScript();
    	$this->renderScript($view);
	}

	/**
	 * Photo upload to any photo album
	 */
	public function uploadphotoAction()
	{
		$photoTable = new Media_Item_Photo();
		$photo = $photoTable->fetchNew();
		if((!$photo->isCreatableBy($this->_user, $this->_acl))){
			$this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
        }

		$albumId = $this->_request->getParam('albumId');
		if($albumId == Media_Album_VideoMain::ID){
			throw new Lib_Exception_Media("Attempt to upload a photo to the video album");
		}

        $photo->albumId = $albumId;
		$photo->mediaType = Media_Item::TYPE_PHOTO;


		$postData = $this->_request->getPost();
		$useRemotePhoto = isset($postData['useRemoteFile'])
						  && ($postData['useRemoteFile'] == '1')
						  && isset($postData['media'])
						  && (strlen($postData['media']) > 0);

//$useRemotePhoto = true;
		$form = $photo->getForm($this->_user, $this->_acl, array('useRemoteFile' => $useRemotePhoto));

		if(empty($postData) || !$form->isValid($postData)){
			// Display empty form or form with errors
			$this->view->form = $form;
			$this->view->album = $album = $this->_getAlbumFromNameOrId(null, $albumId, 0);
    		Zend_Registry::set('Category', $album->getCategory());
    		Zend_Registry::set('SubCategory', $album->getSubCategory());
			return;
        }

        try{
	        // Saving media to database and disk
	        $data = $form->getFormattedValuesForDatabase();
	        $data = array_merge($data, $this->_savePhotoFiles($photo, $form, APP_MEDIA_DIR, uniqid()));
			$this->_helper->dataSaver()->save($photo, $form, $data, $this->_user, $this->_acl, $this->_disregardUpdates);
			$table = new Media_Item_Riders();
			$table->insertRiders($photo->id, $form->riders->getNames());

        } catch (Exception $e) {
        	$this->_cleanUpMedia($photo);
			if($e instanceof Lib_Exception_Media_Photo_Mime){
       			Globals::getLogger()->mediaError("Saving photo for album '$albumId': bad mime type", Zend_Log::ERR);
           		$form->photo->setError('mime');
				$this->view->form = $form;
				return;
           	} else {
       			Globals::getLogger()->mediaError("Failed to save photo for album '$albumId': ".$e->getMessage(), Zend_Log::ERR);
       			$this->_helper->redirectToRoute('othererror', array('error' => 'mediaSavingError'), true);
           	}
        }

        $this->_response->setRedirect($photo->getLink())
                        ->sendResponse();
        exit();
	}

	/**
	 * Photo update
	 */
	public function editphotoAction()
	{
		$photoId = $this->_request->getParam('id');
		$photoTable = new Media_Item_Photo();
		$photo = $this->view->photo = $photoTable->find($photoId)->current();
		$this->view->album = $album = $photo->getAlbum();

		if(empty($photo)){
			throw new Lib_Exception_NotFound("Photo '$photoId' does not exist");
		}
		if((!$photo->isEditableBy($this->_user, $this->_acl))){
			$this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
        }

        $form = $photo->getForm($this->_user, $this->_acl);
        $postData = $this->_request->getPost();
        $dbData = array_merge($photo->toArray(), $this->_helper->populateLocationFormElements($photo, $postData));
        $form->populateFromDatabaseData($dbData);

		if(empty($postData) || !$form->isValid($postData)){
			// Display empty form or form with errors
			$this->view->form = $form;
			$this->render('uploadphoto');
			Zend_Registry::set('Category', $photo->getCategory());
    	    Zend_Registry::set('SubCategory', $photo->getSubCategory());
			return;
        }

        // Form has been submitted
        $oldTitle = $photo->getTitle();
        $oldFiles = array();
        try{
			$temp = new File_Photo($photo->getURI(false));
        	$oldFiles['photo'] = $temp;
        } catch (Lib_Exception $e) {};
        try{
			$temp = new File_Photo($photo->getThumbnailURI(false));
        	$oldFiles['thumb'] = $temp;
        } catch (Lib_Exception $e) {};
        unset($temp);

        $fileInfo = $form->media->getFileInfo();
        $photoSubmitted = !isset($fileInfo['media']['name']) || !empty($fileInfo['media']['name']);
        $uniqid = uniqid();
        $data = $form->getFormattedValuesForDatabase();

		try{
	        if($photoSubmitted){
        		$data = array_merge($data, $this->_savePhotoFiles($photo, $form, APP_MEDIA_DIR, $uniqid));
			}
        	$this->_helper->dataSaver()->save($photo, $form, $data, $this->_user, $this->_acl, $this->_disregardUpdates);
        	$this->_manageOldPhotoFilesOnUpdate($photo, $photoSubmitted, $form->title->getValue(), $uniqid, $oldFiles, $oldTitle);
			$table = new Media_Item_Riders();
			$table->updateRiders($photo->id, $form->riders->getNames());
		} catch (Exception $e) {
			if($e instanceof Lib_Exception_Media_Photo_Mime){
       			Globals::getLogger()->mediaError("Updating photo '$photoId': bad mime type", Zend_Log::ERR);
           		$form->media->addError(Zend_Validate_File_MimeType::FALSE_TYPE);
				$this->view->form = $form;
				$this->render('uploadphoto');
				return;
           	} else {
       			Globals::getLogger()->mediaError("Updating photo '$photoId': ".$e->getMessage(), Zend_Log::ERR);
       			$this->_helper->redirectToRoute('othererror', array('error'=>'mediaUpdatingError'), true);
           	}
		}
        $this->_response->setRedirect($photo->getLink())
                        ->sendResponse();
        exit();

	}

	/**
	 * Video upload
	 */
	public function uploadvideoAction()
	{
		$videoTable = new Media_Item_Video();
		$video = $videoTable->fetchNew();

		if((!$video->isCreatableBy($this->_user, $this->_acl))){
			$this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
        }

		$albumId = $this->_request->getParam('albumId');
		if(in_array($albumId, array(Media_Album_Portfolio::ID, Media_Album_PhotoMain::ID))){
			throw new Lib_Exception_Media("Attempt to upload a video to a photo album");
		}
        $video->albumId = $albumId;
		$video->mediaType = Media_Item::TYPE_VIDEO;
		$form = $video->getForm($this->_user, $this->_acl);

		$postData = $this->_request->getPost();
		if(empty($postData) || !$form->isValid($postData)){
			// Display empty form or form with errors
			$this->view->form = $form;
			$this->view->album = $album = $this->_getAlbumFromNameOrId(null, $albumId, 0);
    		Zend_Registry::set('Category', $album->getCategory());
    		Zend_Registry::set('SubCategory', $album->getSubCategory());
			return;
        }

        try{
	        // Saving media to database and disk
	        $data = $form->getFormattedValuesForDatabase();
			$data = array_merge($data, $this->_saveVideoParameters($video, $form->media));
			if(empty($data['status'])){
				$data['status']= Data::VALID;
			}
	        $this->_helper->dataSaver()->save($video, $form, $data, $this->_user, $this->_acl, $this->_disregardUpdates);
			$table = new Media_Item_Riders();
			$table->insertRiders($video->id, $form->riders->getNames());

        } catch (Exception $e) {
        	Globals::getLogger()->mediaError("Failed to save video for album '$albumId': ".$e->getMessage(), Zend_Log::ERR);
      		$this->_helper->redirectToRoute('othererror', array('error' => 'mediaSavingError'), true);
        }

        $this->_response->setRedirect($video->getLink())
                        ->sendResponse();
        exit();
	}

	/**
	 * Video update
	 */
	public function editvideoAction()
	{
		$videoId = $this->_request->getParam('id');
		$videoTable = new Media_Item_Video();
		$video = $videoTable->find($videoId)->current();
		Zend_Registry::set('Category', $video->getCategory());
		Zend_Registry::set('SubCategory', $video->getSubCategory());

		if(empty($video)){
			throw new Lib_Exception_NotFound("Video '$videoId' does not exist");
		}
		if((!$video->isEditableBy($this->_user, $this->_acl))){
			$this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
        }

        $form = $video->getForm($this->_user, $this->_acl);
        $postData = $this->_request->getPost();
        $dbData = array_merge($video->toArray(), $this->_helper->populateLocationFormElements($video, $postData));
		$form->populateFromDatabaseData($dbData);

		// Display empty form or form with errors
		if(empty($postData) || !$form->isValid($postData)){
			// Remove the Video filter in order to keep raw video html code
			$form->media->removeFilter('Video');
			$form->populateFromDatabaseData($dbData);
			$this->view->form = $form;
			$this->view->album = $video->getAlbum();
			$this->render('uploadvideo');
			return;
        }

        // Form has been submitted
        $data = $form->getFormattedValuesForDatabase();
		$data = array_merge($data, $this->_saveVideoParameters($video, $form->media));
        $this->_helper->dataSaver()->save($video, $form, $data, $this->_user, $this->_acl, $this->_disregardUpdates);
		$table = new Media_Item_Riders();
		$table->updateRiders($video->id, $form->riders->getNames());

        $this->_response->setRedirect($video->getLink())
                        ->sendResponse();
        exit();

	}

	/**
	 * Rotates a photo and its thumbnail by 90, 180 or 270 degrees.
	 */
	public function rotatephotoAction()
	{
		$photoId = $this->_request->getParam('id');
		$table = new Media_Item_Photo();
		$photo = $table->find($photoId)->current();
		if(empty($photo)){
			throw new Lib_Exception_NotFound("Photo not found for id '$photoId'");
		}

        if((!$photo->isEditableBy($this->_user, $this->_acl))){
        	$this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
		}

		$photo->rotate($this->_request->getParam('angle'));
        $this->_response->setRedirect($photo->getLink())
                        ->sendResponse();
        exit();
	}

	/**
	 * Deletes file associated to a photo
	 *
	 * @param Media_Item_Photo_Row $photo
	 */
	protected function _cleanUpPhotoFiles(Media_Item_Photo_Row $photo, $destination = null)
	{
		// Delete a possible thumbnail
		$thumb = APP_MEDIA_DIR . DIRECTORY_SEPARATOR . $photo->getThumbnailURI();
		if(is_file($thumb)){
			Globals::getLogger()->deletes("Cleaning up after upload of photo: deleted thumbnail file '$thumb'", Zend_Log::INFO);
			unlink($thumb);
		}

		// Delete a possible file
		$file = APP_MEDIA_DIR . DIRECTORY_SEPARATOR . $photo->getURI(false);
		if(is_file($file)){
			Globals::getLogger()->deletes("Cleaning up after upload of photo: deleted photo file '$file'", Zend_Log::INFO);
			unlink($file);
		}

		// Delete a possible file without an extension
		if(!empty($destination) && is_file($destination)){
			Globals::getLogger()->deletes("Cleaning up after upload of photo: deleted photo file without an extension '$destination'", Zend_Log::INFO);
			unlink($destination);
		}

		// Delete a possible raw media stored
		$rawMediaFile = APP_MEDIA_DIR_RAW . DIRECTORY_SEPARATOR . $photo->getURI(false);
		if(is_file($rawMediaFile)){
			Globals::getLogger()->deletes("Cleaning up after upload of photo: deleted raw photo file '$rawMediaFile'", Zend_Log::INFO);
			unlink($rawMediaFile);
		}
	}

	/**
	 * Removes all trace of the media from the database
	 *
	 * @param Media_Item_Row $media
	 */
	protected function _cleanUpMedia(Media_Item_Row $media)
	{
		if($media->id){
			$media->delete();
			// Erase any rider tags on this media
			$mediaRidersTable = new Media_Item_Riders();
			$mediaRidersTable->delete(array('id' => $media->id));
		}
	}

	/**
	 * Proceeds to save the photo file, resize it if necessary,
	 * and create the thumbnail
	 *
	 * @param Media_Item_Photo_Row $photo
	 * @param Media_Item_Photo_Form $form
	 * @param string $dir
	 * @param int $id
	 * @return array
	 */
	protected function _savePhotoFiles(Media_Item_Photo_Row $photo, Media_Item_Photo_Form $form, $dir, $id)
	{
		$targetName = $this->_getMediaFileName($form->title->getValue(), $id);
		$destination = $dir . DIRECTORY_SEPARATOR . $targetName;

		$photoElement = $form->media;
		$photoElement->addFilter('Rename', array('target' => $destination));
		if(!$photoElement->receive()) {
			throw new Lib_Exception("An error occured while receiving photo file '{$photoElement->getValue()}'");
		}

		try{
			$photoFile = new File_Photo($destination);
			$photoFile->renameAfterSubType();
			$photoFile->limitDimensions();
			$thumbnail = $photoFile->createThumbnail(APP_MEDIA_THUMBNAILS_DIR, GLOBAL_DEFAULT_IMG_THUMB_WIDTH, GLOBAL_DEFAULT_IMG_THUMB_HEIGHT);
		} catch (Exception $e) {
			$this->_cleanUpPhotoFiles($photo, $destination);
			throw $e;
		}

		return array(
			'path' => GLOBAL_UPLOAD_DEST,
			'uri' => $photoFile->getName(),
			'mediaSubType' => $photoFile->getSubType(),
			'width' => $photoFile->getWidth(),
			'height' => $photoFile->getHeight(),
			'size' => $photoFile->getFileSize(),
			'thumbnailUri' => $thumbnail->getName(),
			'thumbnailWidth' => $thumbnail->getWidth(),
			'thumbnailHeight' => $thumbnail->getHeight(),
			'thumbnailSubType' => $thumbnail->getSubType(),
		);
	}

	protected function _getMediaFileName($title, $uniqid)
	{
		$return = Utils::cleanStringForFilename($title) . '_' . $uniqid;
		return $return;
	}

	/**
	 * Takes care of deleting and renaming of old photo files
	 * when a photo update has been performed
	 *
	 * @param boolean $photoSubmitted
	 * @param string $newTitle
	 * @param string $uniqid
	 * @param array $oldFiles
	 * @param string $oldTitle
	 */
	protected function _manageOldPhotoFilesOnUpdate($photo, $photoSubmitted, $newTitle, $uniqid, $oldFiles, $oldTitle)
	{
        if($photoSubmitted){
        	// Delete old files, since new ones have been created
        	foreach($oldFiles as $oldFile){
        		$oldFile->delete();
        	}
        	return;
        }

        if($oldTitle == $newTitle){
        	return;
        }

        // We need to rename the old files since the title has changed
        $newName = $this->_getMediaFileName($newTitle, $uniqid);
        foreach($oldFiles as $oldFile){
        	$oldFile->rename($newName);
        	$oldFile->renameAfterSubType();
        }
        $photo->thumbnailUri = $photo->uri = $oldFile->getName();
        $photo->save(true);
	}

	/**
	 * Returns an album given its name or id
	 *
	 * @param string $name
	 * @param integer $id
	 * @param string $page
	 * @return Media_Album_Row
	 */
	protected function _getAlbumFromNameOrId($name, $id, $page)
	{
    	switch($name){
    		case Media_Album_PhotoMain::NAME:
    			$id = Media_Album_PhotoMain::ID;
    			break;
    		case Media_Album_VideoMain::NAME:
    			$id = Media_Album_VideoMain::ID;
    			break;
    		case Media_Album_Portfolio::NAME:
    			$id = Media_Album_Portfolio::ID;
    			break;
    		default:
    			if(empty($id)){
					throw new Lib_Exception_NotFound('No album id given');
    			}
    			break;
    	}

		$album = Media_Album_Factory::buildAlbumById($id, $page);

    	return $album;
	}

	/**
	 * Returns information about the video uri, thumbnail dimensions in order to save to database
	 *
	 * @param Media_Item_Video_Row $video
	 * @param Lib_Form_Element_Video $videoElement
	 * @return array
	 * @throws Lib_Exception_Media
	 */
	protected function _saveVideoParameters(Media_Item_Video_Row $video, Lib_Form_Element_Video $videoElement)
	{
		$value = $videoElement->getValue();
		try {
			$parser = new VideoInfoParser();
			$videoInfoObj = $parser->parse($value);
		} catch (Exception $e) {
			throw new Lib_Exception_Media("Could not parse video info: '$value'");
		}
		$thumbnailInfoArr = $this->_getVideoThumbnailInfo($videoInfoObj);
		$thumbnailInfoArr = $this->_saveLocalVideoThumbnail($thumbnailInfoArr);
		return $thumbnailInfoArr;
	}

	protected function _getVideoThumbnailInfo($videoInfoObj)
	{
		$info = array();
		switch($videoInfoObj->getType()){
			case Media_Item_Video::SUBTYPE_YOUTUBE:
				$apiClient = new Google_Api(GOOGLE_APIS_KEY, new Zend_Http_Client());
				$videoData = $apiClient->getYouTubeVideoInfo($videoInfoObj->getId());
				$thumbnail = $videoData['items'][0]['snippet']['thumbnails']['standard'];
				$info['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_YOUTUBE_THUMBNAIL;
				$info['thumbnailUri'] = $thumbnail['url'];
				$info['thumbnailWidth'] = $thumbnail['width'];
				$info['thumbnailHeight'] = $thumbnail['height'];
				break;
			case Media_Item_Video::SUBTYPE_DAILYMOTION:
				$apiClient = new Dailymotion_Api(new Zend_Http_Client());
				$videoData = $apiClient->getVideoInfo($videoInfoObj->getId());
				$info['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_DAILYMOTION_THUMBNAIL;
				$info['thumbnailUri'] = $videoData['thumbnail_480_url'];
				$info['thumbnailWidth'] = 640;
				$info['thumbnailHeight'] = 480;
				break;
			case Media_Item_Video::SUBTYPE_VIMEO:
				$apiClient = new Vimeo_Api(VIMEO_TOKEN, new Zend_Http_Client());
				$videoData = $apiClient->getVideoInfo($videoInfoObj->getId());
				$thumbnail = $videoData['pictures'][0];
				$info['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_VIMEO_THUMBNAIL;
				$info['thumbnailUri'] = $thumbnail['link'];
				$info['thumbnailWidth'] = $thumbnail['width'];
				$info['thumbnailHeight'] = $thumbnail['height'];
				break;
			default:
				throw new Lib_Exception_Media("Unsupported video provider: '".$videoInfoObj->getType()."'");
				break;
		}
		
		if (empty($info['thumbnailUri'])) {
			throw new Lib_Exception_Media("Empty thumbnail url for video of type: '".$videoInfoObj->getType()."'");
		}
		
		$info['mediaSubType'] = $videoInfoObj->getType();
		$info['width'] = $videoInfoObj->getWidth();
		$info['height'] = $videoInfoObj->getHeight();
		$info['uri'] = $videoInfoObj->getId();
		$info['size'] = 0;
		return $info;
	}
	
	protected function _saveLocalVideoThumbnail($params)
	{
		$file = file_get_contents($params['thumbnailUri']);
		if(empty($file)){
			return $params;
		}
		$filename = md5(uniqid(rand())).'.' . $extension;
		$destination = APP_MEDIA_THUMBNAILS_DIR . DIRECTORY_SEPARATOR. $filename;
		file_put_contents($destination, $file);
		$thumbnail = new File_Photo($destination);
		$thumbnail->resize(200, 150);

		$params['thumbnailSubType'] = Media_Item_Photo::SUBTYPE_JPG;
		$params['thumbnailWidth'] = $thumbnail->getWidth();
		$params['thumbnailHeight'] = $thumbnail->getHeight();
		$params['thumbnailUri'] = $thumbnail->getName();

		return $params;
	}
}
