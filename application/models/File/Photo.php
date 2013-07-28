<?php
class File_Photo extends File_Media
{
    /**
     * Image resource
     *
     * @var resource
     */
	protected $_resource;
    /**
     * Name of the function used to load an image in this format
     *
     * @var string
     */
	protected $_loadImageFunction;
    /**
     * Name of the function used to save an image in this format
     *
     * @var string
     */
	protected $_saveImageFunction;

    public function __construct($fullPath)
    {
        parent::__construct($fullPath);

        $this->_type = 'photo';

        /**
         * Mimetype construction
         */
        $imageInfo = getimagesize($fullPath);
		switch($imageInfo["mime"]){
			case 'image/jpg':
			case 'image/jpeg':
				$this->_subType = Media_Item_Photo::SUBTYPE_JPG;
				break;
			case 'image/gif':
				$this->_subType = Media_Item_Photo::SUBTYPE_GIF;
				break;
			case 'image/png':
				$this->_subType = Media_Item_Photo::SUBTYPE_PNG;
				break;
			default:
				throw new Lib_Exception_Media_Photo_Mime("Unknown mime type for photo file: '{$imageInfo["mime"]}'");
				break;
		}
		$this->_width = $imageInfo[0];
		$this->_height = $imageInfo[1];
        $this->_setMimeType($imageInfo["mime"]);

        $this->_checkValidity();
        $this->_updateResource();
    }

    /**
     * Returns this photo's resource
     *
     * @return resource
     */
    public function getResource(){return $this->_resource;}

    /**
     * Sets file extension according to sub-type
     *
     */
    public function renameAfterSubType()
    {
    	switch($this->_subType){
    		case Media_Item_Photo::SUBTYPE_JPG:
    			$extension = 'jpg';
    			break;
    		case Media_Item_Photo::SUBTYPE_GIF:
    			$extension = 'gif';
    			break;
			case Media_Item_Photo::SUBTYPE_PNG:
    			$extension = 'png';
    			break;
    		default:
    			return;
    			break;
    	}
    	$destination = $this->_baseFilename . '.' . $extension;
    	$this->rename($destination, true);
    }

    /**
     * If the photo is bigger than the limits, resize it
     *
     * @param int $maxWidth
     * @param int $maxHeight
     */
    public function limitDimensions($maxWidth = null, $maxHeight = null, $copy = true)
    {
    	if(empty($maxWidth)){
    		$maxWidth = GLOBAL_DEFAULT_IMG_MAX_WIDTH;
    	}
    	if(empty($maxHeight)){
    		$maxHeight = GLOBAL_DEFAULT_IMG_MAX_HEIGHT;
    	}

    	if($this->_width <= $maxWidth && $this->_height <= $maxHeight){
    		// Dimensions are fine
    		return;
    	}

    	if(!$copy){
    		return;
    	}

    	// Save the raw file
    	$this->copy(APP_MEDIA_DIR_RAW.DIRECTORY_SEPARATOR.$this->_currentName);
    	$this->resize($maxWidth, $maxHeight);
    }

    /**
     * Sets up this photo's resource
     */
    protected function _updateResource()
    {
    	$params = $this->_getImageCreationParameters($this->_subType);
    	$createFunction = $params['createFunction'];
    	$this->_resource = $createFunction($this->_fullPath);
        if($this->_resource == null){
            throw new Lib_Exception("An error occured while loading image file '$this->_fullPath'");
        }
    }

    /**
     * Refresh this object according to its corresponding file on disk
     *
     */
    protected function updateInformation()
    {
        parent::updateInformation();
        $this->_updateResource();
    }

    /**
     * Resizes an image (overwrites it)
     */
    public function resize($width, $height, $format = null, $quality = null)
    {
        if(empty($format)){
        	$format = $this->_subType;
        }

        if(empty($quality)){
        	$quality = GLOBAL_DEFAULT_IMG_QUALITY;
        }

    	$newHeight = $height;
    	$newWidth = floor($newHeight / imagesy($this->_resource) * imagesx($this->_resource));

    	if ($newWidth > imagesx($this->_resource)){
            $newWidth = imagesx($this->_resource);
            $newHeight = imagesy($this->_resource);
      	}

        if ($newWidth > $width){
            $newWidth = $width;
            $newHeight = floor($newWidth / imagesx($this->_resource) * imagesy($this->_resource));
      	}

		if(!is_numeric($newHeight) || !is_numeric($newWidth) || $newWidth == 0 || $newHeight == 0 ) {
			throw new Lib_Exception("Invalid dimensions specified for resizinf '$this->_fullPath': width='$newWidth', height='$newHeight'");
      	}
        $resizedImg = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resizedImg, $this->_resource, 0, 0, 0, 0, $newWidth, $newHeight, $this->_width, $this->_height);

        $this->_width = $newWidth;
        $this->_height = $newHeight;
        $this->_resource = $resizedImg;

        // Overwrite source file with resized image:
        $params = $this->_getImageCreationParameters($format);
        
        switch($params['function']){
        	case 'imagejpeg':
        		imagejpeg($resizedImg, $this->_fullPath, $quality);
        		break;
        	case 'imagepng':
        		$pngQuality = ($quality - 100) / 11.111111;
				$pngQuality = round(abs($pngQuality));        		
        		imagepng($resizedImg, $this->_fullPath, $pngQuality);
        		break;
        	case 'imagegif':
        		imagegif($resizedImg, $this->_fullPath);
        		break;

        }
        
        $newName = $this->_baseFilename.'.'.$params['extension'];
        $this->rename($newName);
    }

    /**
     * Returns a resized copy of this photo
     *
     * @param int $width
     * @param int $height
     * @param string $fullPath
     * @param string $format
     * @param int $quality
     * @return File_Photo
     */
    public function resizeTo($width, $height, $fullPath, $format = null, $quality = null)
    {
        $copy = $this->copy($fullPath);
        $copy->resize($width, $height, $format, $quality);
        return $copy;
    }

    /**
     * Creates a thumbnail for this photo
     *
     * @param int $width
     * @param int $height
     * @param string $format a supported photo subType (@see Media_Item_Photo)
     * @return File_Photo
     */
    public function createThumbnail($dir, $width = null, $height = null, $format = null)
    {
        if(empty($format)){
        	$format = Media_Item_Photo::SUBTYPE_JPG;
        }
        if(empty($width)){
        	$width = GLOBAL_DEFAULT_IMG_THUMB_WIDTH;
        }
        if(empty($height)){
        	$height = GLOBAL_DEFAULT_IMG_THUMB_HEIGHT;
        }
        $params = $this->_getImageCreationParameters($format);
        $path = $dir . DIRECTORY_SEPARATOR . $this->_baseFilename.'.'.$params['extension'];
        $thumb = $this->resizeTo($width, $height, $path, $format, GLOBAL_DEFAULT_IMG_THUMB_QUALITY);
        return $thumb;
    }

    public function rotate($angle, $format, $quality = null)
    {
        if(empty($quality)){
        	$quality = GLOBAL_DEFAULT_IMG_QUALITY;
        }

        if(!in_array($angle, array(90,180,270))){
			throw new Lib_Exception_NotFound("angle unsupported: '$angle'");
		}

		$params = $this->_getImageCreationParameters($format);

		$this->_resource = imagerotate($this->_resource, $angle, 0);
		$saveFunction = $this->_saveImageFunction;
		$saveFunction($this->_resource, $this->_fullPath, $quality);
    }

	/**
     * Creates a thumbnail with optional overlay image
     *
     * @param integer $x
     * @param integer $y
     * @param string $overlayImage
     * @param string $appendPath
     * @return FilePicture
     */
    public function resizeWithOverlay($overlayImage='push.gif', $x='', $y='', $destPath='', $quality='')
    {
        /**
         * @todo: check everything
         */
    	throw new Lib_Exception("Not implemented !");
    	if(!is_readable($overlayImage)) throw new Lib_Exception("Cannot read file: $overlayImage");
        if($x == '') $x = Constants::$thumbnailWidth;
        if($y == '') $y = Constants::$thumbnailHeight;
        if($destPath == '') {
            $name = $this->_baseFilename;
            $destPath = Constants::$arrPaths['medias'].'/'.$name."_m.".$this->_extension;
        }

        $thumbnail =  $this->resizeTo($x, $y, $destPath);
        $thumbnailResource = $thumbnail->getResource();

        $overlay = new FilePicture($overlayImage);
        $overlayResource = $overlay->getResource();
        $width = imagesx($overlayResource);
        $height = imagesy($overlayResource);

        $status = imagecopymerge($thumbnailResource, $overlayResource, 5, 5, 0, 0, $width, $height, 100 );
        if(!$status) throw new Lib_Exception("Could not generate overlay: $overlayImage");

        $createImageFunction = $this->_createImageFunction;
        $createImageFunction($thumbnailResource, $destPath, Constants::$imgQuality);
        imagedestroy($thumbnailResource);
        imagedestroy($overlayResource);

        return $thumbnail;
    }

    public function overlay($overlayImage='push.gif', $offsetX, $offsetY)
    {
        /**
         * @todo: check everything
         */
    	throw new Lib_Exception("Not implemented !");
    	if(!is_readable($overlayImage)) throw new Lib_Exception("Cannot read file: $overlayImage");

        $overlay = new FilePicture($overlayImage);
        $overlayResource = $overlay->getResource();
        $width = imagesx($overlayResource);
        $height = imagesy($overlayResource);

        $status = imagecopymerge($this->_resource, $overlayResource, $offsetX, $offsetY, 0, 0, $width, $height, 100 );
        if(!$status) throw new Lib_Exception("Could not generate overlay: $overlayImage");

        $createImageFunction = $this->_createImageFunction;
        $createImageFunction($this->_resource, $this->_fullPath, Constants::$imgQuality);
        imagedestroy($overlayResource);

    }

    /**
     * Performs checks that are specific to media files
     * @throws Lib_Exception
     */
    protected function _checkValidity()
    {
        $readableMaxSize = floor(GLOBAL_DEFAULT_IMG_MAX_FILESIZE/1024);
        if($this->_size > GLOBAL_DEFAULT_IMG_MAX_FILESIZE){
            $sizeKB = floor($this->_size/1024);
            if(($this->_size/(1024*1024)) >= 1 ){
            	$sizeMB = " (".round($this->_size/(1024*1024), 2)."MB)";
            }
            throw new Lib_Exception("File '$this->_fullPath' is too big: {$sizeKB}Ko{$sizeMB}. Maximum: {$readableMaxSize}Ko");
        }
    }

    /**
     * Returns the name of the image creation function necessary to handle
     * the specified image format, and the extension
     *
     * @param string $format
     * @return string
     */
    protected function _getImageCreationParameters($subType)
    {
		switch($subType){
    		case Media_Item_Photo::SUBTYPE_JPG:
    			$function = 'imagejpeg';
    			$createFunction = 'imagecreatefromjpeg';
    			$saveImageFunction = 'imagejpeg';
    			$extension = 'jpg';
				break;
			case Media_Item_Photo::SUBTYPE_GIF:
				$function = 'imagegif';
				$createFunction = 'imagecreatefromgif';
				$saveImageFunction = 'imagegif';
				$extension = 'gif';
				break;
			case Media_Item_Photo::SUBTYPE_PNG:
				$function = 'imagepng';
				$createFunction = 'imagecreatefrompng';
				$saveImageFunction = 'imagepng';
				$extension = 'png';
				break;
			default:
				throw new Lib_Exception("Unsupported format for resizing: '$format'");
				break;
		}

		$this->_saveImageFunction = $saveImageFunction;

		return array(
			'function' => $function,
			'createFunction' => $createFunction,
			'extension' => $extension
		);
    }
}
