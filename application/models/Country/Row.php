<?php
class Country_Row extends Data_Row implements Data_Row_BoundsInterface
{
    /**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = Constants_DataTypes::COUNTRY;

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::COUNTRIES;

    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'displaycountry';

    /**
     * Indicates whether the title is found in another table (true)
     * or directly here (false)
     *
     * @var boolean
     */
    protected $_isTitleTranslated = false;

    /**
     * Indicates whether the description is found in another table (true)
     * or directly here (false)
     *
     * @var boolean
     */
    protected $_isDescriptionTranslated = false;

    /**
     * Default value of notification
     *
     * @var boolean
     */
    protected $_defaultNotification = false;

    public function getDescription()
    {
        throw new Lib_Exception("Countries don't have a description");
    }

	/**
	 * No folders for dpt
	 */
    public function getFolderPath(){}

	/**
	 * No view counter for countries
	 *
	 * @param User_Row $viewer
	 */
    public function viewBy(User_Row $viewer, Zend_Controller_Request_Http $request){}

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
    		$this->bounds = Lib_Geocoder::getBounds($this->getCleanTitle());
    		$this->save();
    	}

    	$bounds = explode(',', $this->bounds);
    	return $bounds;
    }

    public function getCleanTitle()
    {
        $title = Utils::cleanStringForUrl($this->title);
        return $title;
    }

    /**
     * Getter function for the category
     *
     * @return int
     */
    public function getCategory($type = null)
    {
        switch($type){
        	case 'creation':
        		$return = $this->_creationCategory;
        		break;
        	case 'display':
        	default:
        		if($this->id == Country::FRANCE_ID){
        			$return = Category::START;
        		} else {
        			$return = $this->_category;
        		}
        		break;
        }
        return $return;
    }

    /**
     * Getter function for the subcategory
     *
     * @return string
     */
    public function getSubCategory($type = null)
	{
        switch($type){
        	case 'creation':
        		$return = $this->_creationSubCategory;
        		break;
        	case 'display':
        	default:
        		if($this->id == Country::FRANCE_ID){
        			$return = SubCategory::FRANCE;
        		} else {
        			$return = $this->_subCategory;
        		}
        		break;
        }
        return $return;
	}
}