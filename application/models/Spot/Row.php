<?php
/**
 * A spot row behaves like an item, row but also has a Location column and a Dpt column
 *
 */
class Spot_Row extends    Data_Row
               implements Data_Row_LocationInterface,
                          Data_Row_AlbumInterface
{
    /**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = 'spot';

    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'displayspot';

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'editspot';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'createspot';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deletespot';

    /**
     * Name of the route used to construct list urls
     *
     * @var string
     */
    protected $_listRoute = 'listspots';

    /**
     * Foreign key name
     *
     * @var string
     */
    protected $_foreignKeyName = 'spot';

    /**
     * Location of the spot
     *
     * @var Location_Row
     */
    protected $_location;

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Spot_Form';

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::SPOTS;

    /**
     * Whether or not we should create an album when this
     * item is saved
     *
     * @var Boolean
     */
    protected $_createAlbumOnSave = true;

    public function hasLocation()
    {
        $location = $this->getLocation();
    	$return = (!empty($location));
        return $return;
    }

    /**
     * Return the location for current item
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

    public function getSpotType()
    {
        $index = $this->spotType;
        $token = Spot::$spotTypes[$index];
        if(empty($token)){
            return '';
        }
        $string = Globals::getTranslate()->_($token);
        return $string;

    }

    public function getGroundType()
    {
        $index = $this->groundType;
        if(empty($index)){
            $index = 1;
        }
        $string = Spot::$groundTypes[$index];
        if(empty($string)){
            return '';
        }
        $string = Globals::getTranslate()->_($string);
        return $string;
    }

    public function getSubCategory()
	{
		return SubCategory::SPOTS;
	}

    public function getAlbum()
    {
		$album = Media_Album_Factory::buildAggregateItemAlbum($this->getItemType(), $this->id);
		return $album;
    }

	protected function _getAlbumCacheId()
	{
		$cacheId = Media_Album::getCacheId($this->albumId);
		return $cacheId;
	}

	/**
	 * No folders for spots
	 */
    public function getFolderPath(){}

	public function isEditableBy(User_Row $user, Lib_Acl $acl)
	{
		if(in_array($user->status, array(User::STATUS_ADMIN, User::STATUS_EDITOR))){
			return true;
		}

		if(!$this->hasLocation()){
			$status = $acl->isAllowed($user->getRoleId(), Lib_Acl::REGISTERED_RESOURCE);
		} else {
			$resource = $this->_getEditionResourceId();
			if(!$acl->has($resource)){
				return false;
			}
			$status = $acl->isAllowed($user->getOwnerRole(), $resource);
		}

		return $status;
	}

	public function getCityDptAndCountry()
	{
        $city = $dptRow = $countryRow = null;

		if($this->hasLocation()){
			$location = $this->getLocation();
        	$dptRow = $location->getDpt();
        	$countryRow = $location->getCountry();
        	$city = ucfirst($location->city);
        } elseif(!empty($this->dpt)){
        	$msg = "Spot {$this->id} has no location, but has a dpt: '{$this->dpt}'.";

        	$dptTable = new Dpt();
            $dptRow = $dptTable->find($this->dpt)->current();
        	if(empty($dptRow)){
        		$msg .= " Dpt '{$this->dpt}' could not be found in database.";
        	}

        	Globals::getLogger()->locations($msg);
        } else {
        	$msg = "Spot {$this->id} has no location and no dpt";
        	Globals::getLogger()->locations($msg);
        }

		return array($city, $dptRow, $countryRow);
	}

	/**
	 * Returns a string representing the content.
	 * Used for search exceprt generation.
	 */
	public function getFlatContent()
	{
		$return = ucfirst($this->getTitle()).' '.$this->getDescription().' '.$this->getSpotType().' '.$this->getGroundType();
		if($this->hasLocation()){
			$location = $this->getLocation();
			$return .= ' '.$location->city.' ';
			if($dpt = $location->getDpt()){
				$return .= ' '.$dpt->getTitle();
			}
			if($country = $location->getCountry()){
				$return .= ' '.$country->getTitle();
			}
		}

		$return .= ' '.implode(' ', $this->getTags());
		return $return;
	}
}