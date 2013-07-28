<?php
class Dpt_Row extends Data_Row implements Data_Row_BoundsInterface
{
    /**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = Constants_DataTypes::DPT;

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::DPT;

    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'displaydpt';    
    
    /**
     * Indicates whether the title is found in another table (true)
     * or directly here (false)
     *
     * @var boolean
     */
    protected $_isTitleTranslated = false;


    public function getDescription()
    {
        throw new Lib_Exception("Dpt don't have a description");
    }

    public function getPrefixedTitle()
    {
    	/**
    	 * @todo: gerer les prefixes
    	 */
    }

	/**
	 * No folders for dpt
	 */
    public function getFolderPath(){}

	/**
	 * No view counter for dpt
	 *
	 * @param User_Row $viewer
	 * @param boolean $andSave
	 */
    public function viewBy(User_Row $viewer, Zend_Controller_Request_Http $request)
	{
		
	}

    /**
     * Return the department for current item
     *
     * @return Location_Row
     */
    public function getLocation()
    {
        if(empty($this->id)){
        	// This was never saved: can't have a location!
        	return null;
        }
    	
    	$cacheId = $this->_getLocationCacheId();
        $cache = $this->getCache();
        
        /*
         * How can we differentiate 'no location' and 'no cache'?
         * We save 0 in cache if no location.
         */
        $noLocationMarker = 0;
        
		$table = new Location();
        $location = $cache->load($cacheId);
		if($location instanceof Location_Row){
        	$location->setTable($table);
        	return $location;
        } elseif($location === $noLocationMarker){
        	return null;
        }
        
        $where = "itemType = '".$this->getItemType()."' AND itemId = ".$this->getId();
        $location = $table->fetchRow($where);
        if($location === null){
        	$cache->save($noLocationMarker, $cacheId);
        	return null;
        }
        
        $cache->save($location, $cacheId);
        return $location;
    	
    }

    public function getBounds()
    {
    	if(empty($this->bounds)){
    		$this->bounds = Lib_Geocoder::getBounds($this->getCleanTitle(). ', '. $this->getCountry()->getCleanTitle());
    		$this->save();
    	}
    	
    	$bounds = explode(',', $this->bounds);
    	return $bounds;
    }
    
	public function getCountry()
	{    
        if(empty($this->country)){
            return null;
        }
        $country = $this->findParentRow('Country');
        return $country;
	}
}