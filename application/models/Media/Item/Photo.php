<?php
class Media_Item_Photo extends Media_Item
{
    const SUBTYPE_JPG = 'jpg';
    const SUBTYPE_PNG = 'png';
    const SUBTYPE_GIF = 'gif';
    const SUBTYPE_FLICKR = 'flickr';
    const SUBTYPE_VIMEO_THUMBNAIL = 'vimeo_thumb';
    const SUBTYPE_YOUTUBE_THUMBNAIL = 'youtube_thumb';
    const SUBTYPE_DAILYMOTION_THUMBNAIL = 'dailymotion_thumb';

    const MIME_JPG = 'image/jpg';
    const MIME_PNG = 'image/png';
    const MIME_GIF = 'image/gif';
    
    /**
     * Name of the class representing a row
     *
     * @var string
     */
    protected $_rowClass = 'Media_Item_Photo_Row';

    protected $_itemType = 'photo';
    
    public static $allowedMediaSubTypes = array(
    	self::SUBTYPE_JPG,
    	self::SUBTYPE_PNG,
    	self::SUBTYPE_GIF,
    	self::SUBTYPE_FLICKR,
	);
    
    public static $allowedMimeTypes = array(
    	self::MIME_JPG,
    	self::MIME_PNG,
    	self::MIME_GIF,
	);
    
	/**
     * Get A list of random photos.
     * Given that some pictures might be private,
     * this function will only return pictures belonging to
     * the main photo album or the portfolio album.
     *
     * @param integer $amount
     * @return array
     */
    public function getRandom($amount = 1)
    {
    	$amount = is_integer($amount) ? $amount : 1;
    	$authorisedAlbums = array(
    		Media_Album_PhotoMain::ID,
    		Media_Album_Portfolio::ID
    	);
    	$authorisedAlbums = implode(', ', $authorisedAlbums);
    	
    	$data = $this->fetchAll('status = "'.self::VALID.'" AND albumId IN ('.$authorisedAlbums.')', 'RAND()', $amount);
    	return $data;
    }

    /**
     * Get the latest photos.
     * Given that some pictures might be private,
     * this function will only return pictures belonging to
     * the main photo album or the portfolio album.
     * 
     * @param integer $amount
     * @return array
     */
    public function getLatest($amount = 1)
    {
    	$amount = is_integer($amount) ? $amount : 1;
    	$authorisedAlbums = array(
    		Media_Album_PhotoMain::ID,
    		Media_Album_Portfolio::ID
    	);
    	$authorisedAlbums = implode(', ', $authorisedAlbums);
    	
    	$data = $this->fetchAll('status = "'.self::VALID.'" AND albumId IN ('.$authorisedAlbums.')', 'id DESC', $amount);
    	return $data;
    }	
	
    public static function getAllowedExtensionsString()
    {
    	return 'jpg,jpeg,png,gif';
    }
    
    public static function getAllowedMimeTypes()
    {
    	return implode(',', self::$allowedMimeTypes);
    }
}