<?php
class Forum_Row extends Data_Row
{
    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'forum';

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Forum_Form';

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::FORUMS;

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
     * Returns the url for the page describing the current object
     *
     * @return string
     */
    public function getLink()
    {
        $params = array(
            'name' => Utils::cleanStringForUrl($this->getTitle()),
            'id' => $this->id,
        	'page' => 1
        );
        $link = Globals::getRouter()->assemble($params, $this->_route, true);
        return $link;
    }

	public function isReadableBy(User_Row $user, Lib_Acl $acl)
	{
    	if($acl->isAllowed($user->getRoleId(), $this->_getReadResourceId())){
    		// Public forum and regular user, or private forum and editor/admin
    		return true;
    	}

    	$status = $this->_checkAccess(Forum_Access::READ, $user);
    	return $status;
	}

    /**
     * Defined by Zend_Acl_Resource_Interface
     * Build a string that ties the submitter to the 'user-submitted, public' resource
     *
     * @return string
     */
    protected function _getReadResourceId()
    {
        if($this->privacy == Forum::PUBLIC_FORUM){
    		$string = Lib_Acl::FORUM_PUBLIC_READ_RESOURCE;
    	} else {
    		$string = Lib_Acl::FORUM_PRIVATE_READ_RESOURCE;
    	}

        return $string;
    }

    /**
     * Returns the resource for creating a new instance of this object
     *
     * @return string
     */
    protected function _getCreationResourceId()
    {
        $string = Lib_Acl::ADMIN_RESOURCE;
        return $string;
    }

    protected function _getEditionResourceId()
    {
        if($this->privacy == Forum::PUBLIC_FORUM){
    		$string = Lib_Acl::FORUM_PUBLIC_POST_RESOURCE;
    	} else {
    		$string = Lib_Acl::FORUM_PRIVATE_POST_RESOURCE;
    	}

        return $string;
    }

    /**
     * Returns the date that this forum was last updated
     *
     * @return string
     */
	public function getLastPostDate()
	{
        if(empty($this->lastPostDate)){
            return null;
        }
        $date = Lib_Date::getFormattedDate($this->lastPostDate);
        return $date;
	}

	/**
	 * Returns the last user who posted to this forum
	 *
	 * @return User_Row
	 */
	public function getLastPoster()
	{
        if(empty($this->lastPoster)){
            return null;
        }
        $lastPoster = $this->findParentRow('User','LastPoster');
        return $lastPoster;
	}

	/**
	 * Returns a list of moderators for this forum
	 * @return array
	 */
	public function getModerators()
	{
		$userTable = new User();

		$moderatorList = $this->getAccessList('access = '.Forum_Access::MODERATE);
		$userIdList = array();
		foreach ($moderatorList as $row){
			$userIdList[] = $row['userId'];
		}
		if(empty($userIdList)){
			return array();
		}
		$moderators = $userTable->fetchAll('userId IN ('.implode(', ', $userIdList).')');
		return $moderators;
	}

	/**
	 * Returns a rowset that contains userId's whose access field
	 * matches criteria
	 *
	 * @param string $where
	 * @return Zend_Db_Table_Rowset
	 */
	public function getAccessList($where = null)
	{
		$table = new Forum_Access();
		$select = $table->select()->where('forumId = ?', $this->id );
		if($where){
			$select->where($where);
		}
		$rowSet = $table->fetchAll($select);
		return $rowSet;
	}

	public function getTopics()
	{
    	$cacheId = $this->_getTopicsCacheId();
        $cache = $this->getCache();
		$topics = $cache->load($cacheId);
	    if(!$topics){
			$select = $this->getTable()->select();
			$select->order('id ASC');
			$topics = $this->findDependentRowset('Forum_Topic', null, $select);
			$this->getTable()->saveDataInCache($cache, $topics, $cacheId);
		} else {
			$topics->setTable(new Forum_Topic());
		}
		return $topics;
	}
	
    protected function _getTopicsCacheId()
    {
    	$return = 'topicsForForum'.$this->getId();
    	return $return;
    }	

	public function checkTopicPostAcces($user)
	{
    	if($this->privacy == Forum::PUBLIC_FORUM){
    		return true;
    	}
    	
		$status = $this->_checkAccess(Forum_Access::POST, $user);
	    return $status;
	}

    /**
     * Checks whether the current user may access the given forum
     * for the given access level
     *
     * @param Forum_Row $forum
     * @param integer $access
     * @return boolean
     */
    protected function _checkAccess($access, $user)
    {
    	$table = new Forum_Access();
    	$accessRow = $table->find($this->id, $user->{User::COLUMN_USERID})->current();
    	if(empty($accessRow)){
    		// No access defined for this user and this forum.
    		return false;
    	}

    	if($accessRow->access >= $access){
    		return true;
    	}

    	return false;
    }

	/**
	 * No folders for forums
	 */
    public function getFolderPath(){}

    /**
     * Returns a list of all cache id's that must be emptied
     * when saving/deleting this object.
     */
    protected function _getCacheIdsForClear()
    {
		$return = parent::_getCacheIdsForClear();
    	$return[] =$this->_getTopicsCacheId();
		return $return;    
    }
}