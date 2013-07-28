<?php
class News_Row extends Article_Row implements Data_Row_LocationInterface,
											  Data_Row_SpotInterface
{
	/**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = 'news';

    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'displaynews';

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'editnews';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'createnews';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deletenews';

    /**
     * Name of the route used to construct list urls
     *
     * @var string
     */
    protected $_listRoute = 'listnews';

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'News_Form';

    /**
     * Name of the class of form used to edit the rest of the attributes 
     * of this object before it is activated
     *
     * @var string
     */
    protected $_subForm2Class = 'News_Form_SubForm2';
    
    /**
     * Location of the spot
     *
     * @var Location_Row
     */
    protected $_location;

    /**
     * Category
     *
     * @var int
     */
    protected $_category = Category::ARTICLES;

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::NEWS;

    /**
     * Default creation category
     *
     * @var int
     */
    protected $_creationCategory = Category::EDITION;

    /**
     * Default creation subcategory
     *
     * @var int
     */
    protected $_creationSubCategory = SubCategory::CREATENEWS;

    /**
     * Name of the layouts used to display this item
     *
     * @var string
     */
    protected $_layouts = array(
    	Data::ACTION_LIST => 'two-columns',
    	Data::ACTION_DISPLAY => 'one-column',
    );

    /**
     * Return the spot for current item
     *
     * @return Spot_Row
     */
    public function getSpot()
    {
        $spot = $this->findParentRow('Spot');
        return $spot;
    }

    public function hasSpot()
    {
        return (!empty($this->spot));
    }

    public function getSpotName()
    {
        if(empty($this->spot)){
            return null;
        }

        $spotInfo = $this->getSpotNameAndLink();
        return $spotInfo['name'];
    }

    public function getSpotNameAndLink()
    {
        $return = array(
            'name' => null,
            'link' => null,
        );

        if(empty($this->spot)){
            return $return;
        }

        if(strpos($this->spot, NOREALDATA_MARK) !== false){
            // spotname is stored directly in DB
            $return['name'] = str_replace(NOREALDATA_MARK, '', $this->spot);
            return $return;
        }

        // spotId is stored in DB
        $spot = $this->findParentRow('Spot');
        if(!empty($spot)){
            $return['name'] = $spot->getTitle();
            $return['link'] = $spot->getLink();
        }
        return $return;
    }

    public function hasLocation()
    {
        $location = $this->getLocation();
    	$return = (!empty($location));
        return $return;
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

    public function setLocation(Location_Row $location)
    {
        $this->_location = $location;
    }

	/**
	 * Returns the whole path for the folder associated 
	 * to this object
	 */
    public function getFolderPath()
	{
		$path = CONTENT_DIRECTORY_NEWS. $this->getFolderName();
		return $path;
	}

}
