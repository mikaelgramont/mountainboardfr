<?php
abstract class Media_Album_Row extends Data_Row
{
    /**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = 'album';

    protected $_route = 'displayalbum';

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'editalbum';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'createalbum';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deletealbum';

    /**
     * Name of the route used to construct list urls
     *
     * @var string
     */
    protected $_listRoute = 'listalbum';

    /*
     * Media items rowset
     * @var Media_Item_Rowset
     */
	protected $_itemRowset = null;

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Media_Album_Form';

    /**
     * Parent of the current Album.
     * Null for the main albums.
     *
     * @var stdClass
     */
    protected $_parentItem;

    /**
     * Default value of notification
     *
     * @var boolean
     */
    protected $_defaultNotification = false;

	/**
     * Defined by Zend_Acl_Resource_Interface
     * Build a string that ties the submitter to the 'user-submitted, public' resource
     *
     * @return string
     */
    protected function _getReadResourceId()
    {
    	switch($this->albumAccess){
    		case Media_Album::ACCESS_PRIVATE:
    			$string = Lib_Acl::PRIVATE_READ_RESOURCE. '_'.$this->submitter;
    			break;
    		case Media_Album::ACCESS_PUBLIC:
    			$string = Lib_Acl::PUBLIC_READ_RESOURCE;
    			break;
    	}

        return $string;
    }

	public function getItemSet()
	{
		if($this->_itemRowset === null){
			$this->_setupItems();
		}
		return $this->_itemRowset;
	}

	protected function _setupItems()
	{
		$albumItems = $this->_getItems();
		$this->_itemRowset = new Media_Item_Set($albumItems, Globals::getUser(), Globals::getAcl(), $this->page);
	}

	public function getItemsCacheId()
	{
		return 'ItemsForAlbum_'.$this->getId();
	}

    /**
     * Returns a list of all cache id's that must be emptied
     * when saving/deleting this object.
     */
    protected function _getCacheIdsForClear()
    {
		$return = parent::_getCacheIdsForClear();
		$return[] = $this->getItemsCacheId();    	
		return $return;    
    }	
	
	/**
	 * Returns the name of the view script to use when rendering
	 *
	 * @return string
	 */
	public function getViewScript()
	{
		switch($this->id){
			default:
				$view = 'media/displayalbum.phtml';
				break;
		}
		return $view;
	}

	public function getAmountPerPage()
	{
	    return MEDIA_PER_PAGE;
	}

    public function getPreviousItem(Media_Item_Row $currentItem)
    {
        $previousItem = $this->getItemSet()->getPreviousItem($currentItem);
        return $previousItem;
    }

    public function getNextItem(Media_Item_Row $currentItem)
    {
        $nextItem = $this->getItemSet()->getNextItem($currentItem);
        return $nextItem;
    }

    public function getLink()
    {
        switch($this->id){
            case Media_Album_PhotoMain::ID:
                $link = Globals::getRouter()->assemble(array(), 'photos');
                break;
            case Media_Album_VideoMain::ID:
                $link = Globals::getRouter()->assemble(array(), 'videos');
                break;
            case Media_Album_Portfolio::ID:
                $link = Globals::getRouter()->assemble(array(), 'portfolio');
                break;
            default:
				$params = array(
            		'albumName' => $this->getCleanTitle(),
            		'albumId' => $this->id,
        		);
        		$link = Globals::getRouter()->assemble($params, $this->_route, true);

                /*
        		$params = array(
                    'albumName' => $this->getCleanTitle(),
                    'albumId' => $this->id,
                );
                $link = Globals::getRouter()->assemble($params, $this->_route, true);
				*/
                break;
        }
        return $link;
    }

	/**
	 * Returns this item's parent
	 *
	 * @return mixed stdClass|Null
	 */
    public function getParent()
	{
		return $this->_parentItem;
	}

	public function isEditableBy(User_Row $user, Lib_Acl $acl)
	{
		if(in_array($user->status, array(User::STATUS_ADMIN, User::STATUS_EDITOR))){
			return true;
		}

		$resource = $this->_getEditionResourceId();
		if(!$acl->has($resource)){
			return false;
		}

		/**
		 * This is how we define a user owns an album:
		 *  - they created it
		 *  - or the album was created when they registered
		 */
		if($this->albumType == Media_Album::TYPE_SIMPLE && $this->albumCreation = Media_Album::CREATION_USER){
			return true;
		}
		if($this->albumType == Media_Album::TYPE_AGGREGATE && $this->albumCreation = Media_Album::CREATION_AUTOMATIC){
			$aggTable = new Media_Album_Aggregation();
			$where = "albumId = $this->id AND keyValue = " . $user->{User::COLUMN_USERID} ." AND keyName = 'rider'";
			$exists = $aggTable->fetchRow($where);
			if($exists){
				return true;
			}
		}
		return false;
	}

	public function isDeletableBy(User_Row $user, Lib_Acl $acl)
	{
		throw new Lib_Exception("Album deletion rigths not checked yet: @todo");
	}

	/**
	 * No folders for media albums
	 */
    public function getFolderPath(){}
}