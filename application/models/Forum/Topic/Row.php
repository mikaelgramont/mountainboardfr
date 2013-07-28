<?php
class Forum_Topic_Row extends Data_Row
{
    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'topic';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'newtopic';

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'edittopic';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deletetopic';

	/**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Forum_Topic_Form';

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
	 * Returns the parent forum
	 *
	 * @return Forum_Row
	 */
    public function getForum()
	{
		$forum = $this->findParentRow('Forum');
		return $forum;
	}

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
            'page' => 1,
        );
        $link = Globals::getRouter()->assemble($params, $this->_route, true);
        return $link;
    }

    /**
     * Returns the url of the last page of this topic
     *
     * @return string
     */
    public function getLastPageLink()
    {
		$posts = $this->getPosts();
    	$pages = ceil(count($posts) / POSTS_PER_PAGE);

    	$params = array(
            'name' => Utils::cleanStringForUrl($this->getTitle()),
            'id' => $this->id,
            'page' => $pages,
        );
        $link = Globals::getRouter()->assemble($params, $this->_route, true);
        return $link;
    }

    protected function _getPostsCacheId()
    {
    	$return = 'postsForTopic'.$this->getId();
    	return $return;
    }

    /**
     * Returns the url for the submission of a new topic
     *
     * @return string
     */
    public function getCreateLink()
    {
    	$link = Globals::getRouter()->assemble(array('dataType' => 'sujet', 'forumId' => $this->forumId), $this->_createRoute, true);
        return $link;
    }

    /**
     * Returns the url for the submission of a new topic
     *
     * @return string
     */
    public function getDeleteLink()
    {
    	$params = array(
    		'name' => $this->getCleanTitle(),
    		'id' => $this->id
    	);
    	$link = Globals::getRouter()->assemble($params, $this->_deleteRoute, true);
        return $link;
    }

    /**
     * Returns the url for the edition page of the current topic
     *
     * @return string
     */
    public function getEditLink()
    {
        if(empty($this->id)){
            return $this->getCreateLink();
        }

        $params = array(
            'id' => $this->id,
        );
        $link = Globals::getRouter()->assemble($params, $this->_editRoute, true);
        return $link;
    }

	public function isCreatableBy(User_Row $user, Lib_Acl $acl)
	{
	    if(in_array($user->status, array(User::STATUS_ADMIN, User::STATUS_EDITOR))){
			return true;
		}

		$forum = $this->getForum();
		if(!$forum){
			throw new Lib_Exception_NotFound("No forum found for topic ".$this->getId());
		}

        if($forum->privacy == Forum::PUBLIC_FORUM){
            $resource = $this->_getCreationResourceId();
    		$status = $acl->isAllowed($user->getRoleId(), $resource);
    		return $status;
        }

        $status = $forum->checkTopicPostAcces($user);
        return $status;
	}

	public function isReadableBy(User_Row $user, Lib_Acl $acl)
	{
		$return = $this->getForum()->isReadableBy($user, $acl);
		return $return;
	}

    /**
     * Defined by Zend_Acl_Resource_Interface
     * Build a string that ties the submitter to the 'user-submitted, public' resource
     *
     * @return string
     */
    protected function _getReadResourceId()
    {
        $forum = $this->getForum();
    	if($forum->privacy == Forum::PUBLIC_FORUM){
    		$string = Lib_Acl::FORUM_PUBLIC_READ_RESOURCE;
    	} else {
    		$string = Lib_Acl::FORUM_PRIVATE_READ_RESOURCE;
    	}

        return $string;
    }

    /**
     * Returns the description, from this table or another one
     *
     * @return string
     */
    public function getDescription()
    {
       	throw new Lib_Exception("Forum topics cannot be asked for their description");
    }

	public function getLastPostDate()
	{
        if(empty($this->lastPostDate)){
            return null;
        }
        $date = Lib_Date::getFormattedDate($this->lastPostDate);
        return $date;
	}

	public function getLastPoster()
	{
        if(empty($this->lastPoster)){
            return null;
        }
        $lastPoster = $this->findParentRow('User','LastPoster');
        return $lastPoster;
	}

	/**
     * Returns the first post for this topic
     *
     * @return Forum_Post_Row
     */
    public function getFirstPost()
    {
		$posts = $this->getPosts();

		if(!count($posts)){
			throw new Lib_Exception_Forum("Topic $this->id has no posts");
		}

		return $posts[0];
    }

	public function getLastPost()
	{
		$posts = $this->getPosts();

		if(!count($posts)){
			throw new Lib_Exception_Forum("Topic $this->id has no posts");
		}
		return $posts[count($posts) - 1];
	}

	public function getPosts()
	{
    	$cacheId = $this->_getPostsCacheId();
        $cache = $this->getCache();
		$posts = $cache->load($cacheId);
	    if(!$posts){
			$select = $this->getTable()->select();
			$select->order('id ASC');
			$posts = $this->findDependentRowset('Forum_Post', null, $select);
			$this->getTable()->saveDataInCache($cache, $posts, $cacheId);
		} else {
			$posts->setTable(new Forum_Post());
		}
		return $posts;
	}

	/**
	 * Delete this topic and all related posts
	 */
	public function delete()
	{
		$posts = $this->getPosts();
		foreach($posts as $post){
			$post->delete();
		}

		parent::delete();
	}

	/**
	 * No folders for forum topics
	 */
    public function getFolderPath(){}

    /**
     * Call special formatting functions before populating
     * form with data form database
     *
     * @param array $data
     */
    public function populateFromDatabaseData(array $data, $exceptionOnEmptyText = true)
    {
    	if($this->_object->id){
    		// Only get title and content from the database if they are there already
    		$data[Data_Form_Element::TITLE] = $this->_object->getTitle($exceptionOnEmptyText);
    		$data[Data_Form_Element::CONTENT] = $this->_object->getFirstPost()->getContent($exceptionOnEmptyText);
    	}
    	parent::populateFromDatabaseData($data);
    }

    /**
     * Returns a list of all cache id's that must be emptied
     * when saving/deleting this object.
     */
    protected function _getCacheIdsForClear()
    {
		$return = parent::_getCacheIdsForClear();
    	$return[] =$this->_getPostsCacheId();
		return $return;
    }

	/**
	 * Returns a string representing the content.
	 * Used for search exceprt generation.
	 */
	public function getFlatContent()
	{
		$return = $this->getTitle();
		foreach($this->getPosts() as $post){
			$return .= ' '.$post->getFlatContent();
		}

		return $return;
	}

}
