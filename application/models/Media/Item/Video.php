<?php
class Media_Item_Video extends Media_Item
{
    const SUBTYPE_YOUTUBE = 'youtube';
    const SUBTYPE_VIMEO = 'vimeo';
    const SUBTYPE_DAILYMOTION = 'dailymotion';

    public static $allowedMediaSubTypes = array(
    	self::SUBTYPE_YOUTUBE,
    	self::SUBTYPE_VIMEO,
    	self::SUBTYPE_DAILYMOTION,
	);
    
	/**
     * Name of the class representing a row
     *
     * @var string
     */
    protected $_rowClass = 'Media_Item_Video_Row';
    
	protected $_itemType = 'video';

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
    		Media_Album_VideoMain::ID,
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
    		Media_Album_VideoMain::ID,
    	);
    	$authorisedAlbums = implode(', ', $authorisedAlbums);
    	
    	$data = $this->fetchAll('status = "'.self::VALID.'" AND albumId IN ('.$authorisedAlbums.')', 'id DESC', $amount);
    	return $data;
    }		
	
	/**
	 * Returns the regex used to make sure a correct video html code was submitted
	 *
	 * @return string
	 */
	public static function getCleanVideoCodeRegex()
	{
		$regex = '#<div class="([a-z]+)-embed"><span class="width">([0-9]{2,3})</span><span class="height">([0-9]{2,3})</span>([A-Za-z0-9\-_]+)</div>#';
		return $regex;		
	}
}