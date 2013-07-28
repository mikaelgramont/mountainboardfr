<?php
/**
 * A spot row behaves like an item, row but also has a Location column
 * and a Spot Column
 *
 */
class Event_Row extends    Article_Row
                implements Data_Row_ArticleInterface,
                           Data_Row_SpotInterface
{
    /**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = Constants_DataTypes::EVENT;

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'editevent';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'createevent';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_route = 'displayevent';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deleteevent';

    /**
     * Default category
     *
     * @var int
     */
    protected $_category = Category::START;

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::EVENTS;

    /**
     * Default creation category
     *
     * @var int
     */
    protected $_creationCategory = Category::EDITION;

    /**
     * Default creation category
     *
     * @var int
     */
    protected $_creationSubCategory = SubCategory::CREATEEVENT;

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Event_Form';

    /**
     * Whether this object is fully saved with one form (true),
     * or if we must go through two passes (false).
     *
     * @var boolean
     */
    protected $_onePassSubmit = true;

    /**
     * Name of the class of form used to submit the title of this object
     * the very first time the object is going to be saved
     *
     * @var string
     */
    protected $_subForm1Class = null;

    /**
     * Name of the class of form used to edit the rest of the attributes
     * of this object before it is activated
     *
     * @var string
     */
    protected $_subForm2Class = null;

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
     * Instantiates the form to edit this document.
     * If the id is empty, we are editing a brand new document.
     * If the id is not empty, but the date is, that means we just submitted
     * the document and it was never activated.
     * If the id is not empty and the date either, then we are editing a
     * document that was activated before.
     *
     * @param User_Row $user
     * @param Lib_Acl $acl
     * @param array $options
     * @return Data_Form
     */
    public function getForm(User_Row $user, Lib_Acl $acl, $options = null)
    {
   		$formClass = $this->_formClass;
    	$form = new $formClass($this, $user, $acl, $options);
        $form->setName($this->getItemType().'Form');
        return $form;
    }

    protected function _doInsert($setInValid = false)
    {
        parent::_doInsert($setInValid);
        // Album creation
        Media_Album::createAggregateAlbumFor($this);
    }

    protected function _postDelete()
    {
        parent::_postDelete();
        // Album deletion
        Media_Album::deleteAggregateAlbumFor($this);
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
     * Return the spot for current item
     *
     * @return Spot_Row
     */
    public function getSpot()
    {
        if(empty($this->spot)){
        	return null;
        }
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

	/**
	 * Returns the whole path for the folder associated
	 * to this object
	 */
    public function getFolderPath()
	{
		$path = CONTENT_DIRECTORY_EVENTS . $this->getFolderName();
		return $path;
	}

    /**
     * Returns the content of this article
     *
     * @return string
     */
    public function getContent()
    {
    	$content = $this->content;
        return $content;
    }

	protected function _getCacheIdsForClear()
	{
		$cacheIds = parent::_getCacheIdsForClear();
		$cacheIds[] =  $this->getTable()->getNextEventsAfterCacheId(date('Y-m-d'));
		return $cacheIds;
	}
}