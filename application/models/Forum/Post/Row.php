<?php
class Forum_Post_Row extends Data_Row implements Data_Row_MetaDataInterface
{
    /**
     * This post's parent forum topic
     * @var Forum_Topic_Row
     */
	public $topic;

    /**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = 'post';

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::FORUMS;

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Forum_Post_Form';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'newpost';

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'editpost';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deletepost';

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
     * Returns the url for the submission of a new post
     *
     * @return string
     */
    public function getCreateLink()
    {
    	$topic = $this->getTopic();
    	$link = Globals::getRouter()->assemble(array('dataType' => 'sujet', 'topicId' => $topic->id), $this->_createRoute, true);
        return $link;
    }

    /**
     * Returns the url for the edition page of the current post
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

    /**
     * Returns the url for deleting an object
     *
     * @return string
     */
    public function getDeleteLink()
    {
        $params = array(
            'id' => $this->id
        );
        $link = Globals::getRouter()->assemble($params, $this->_deleteRoute, true);
        return $link;
    }

	/**
	 * Returns the parent topic
	 *
	 * @return Forum_Topic_Row
	 */
    public function getTopic()
	{
		if(empty($this->topic)){
			$this->topic = $this->findParentRow('Forum_Topic');
		}
		return $this->topic;
	}

    public function getLink()
    {
        $topic = $this->getTopic();
        if(empty($topic)){
        	throw new Lib_Exception_Forum("Topic $this->topicId for post $this->id does not exist");
        }
        $posts = $topic->getPosts();
        foreach($posts as $index => $post){
            if($post->id == $this->id){
                break;
            }
        }

        $page = 1 + floor($index / POSTS_PER_PAGE);
        unset($posts);
        $params = array(
            'id' => $topic->id,
            'name' => $topic->getCleanTitle(),
            'page' => $page
        );
        $url  = Globals::getRouter()->assemble($params, 'topic', true);
        $url .= '#forumPostId'.$this->id;

        return $url;
    }

    public function getTone()
    {
    	return $this->tone;
    }


	/**
	 * Returns the url that the user will be redirected to
     * upon deletion of this object
	 *
	 * @return string
	 */
	protected function _getDeleteRedirectUrl($params, User_Row $user)
	{
		$url = $this->getTopic()->getLink();
		return $url;
	}

    /**
     * Returns the parent item
     * @return Data_Row
     * @throws Lib_Exception
     */
    public function getParentItemfromDatabase()
    {
        return $this->getTopic();
    }

    /**
     * Returns the parent item
     * @return Data_Row
     * @throws Lib_Exception
     */
    public function getParentItem()
    {
        //return $this->getTopic();

        $parentRow = $this->getParentItemfromDatabase();

        $itemTable = new Item();
        $select = $itemTable->select();
        $select->where('itemId = '.$parentRow->id)
               ->where('itemType = "'.$parentRow->getItemType().'"');


        $parentItem = $itemTable->fetchRow($select);
        return $parentItem;
    }

	/**
	 * No folders for forum posts
	 */
    public function getFolderPath(){}

    /**
     * Returns the title, from this table or another one
     *
     * @return string
     */
    public function getTitle()
    {
       	return Globals::getTranslate()->_('postTitle');
    }

    /**
     * Returns the description, from this table or another one
     *
     * @return string
     */
    public function getDescription()
    {
       	throw new Lib_Exception("Forum posts cannot be asked for their description");
    }

    /**
     * Returns the content of this object
     *
     * @return string
     */
    public function getContent()
    {
    	$content = $this->content;
        return $content;
    }

	/**
	 * Returns a string representing the content.
	 * Used for search exceprt generation.
	 */
	public function getFlatContent()
	{
		$return = strip_tags($this->getContent());
		return $return;
	}

    /**
     * Returns a list of all cache id's that must be emptied
     * when saving/deleting this object.
     */
    protected function _getCacheIdsForClear()
    {
		$return = array(
    	);

       	$return[] = $this->getParentRowCacheId('User', 'LastEditor');

		return $return;
    }
}