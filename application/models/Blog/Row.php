<?php
class Blog_Row extends Data_Row
{
    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'blog';

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'editblog';

	protected $_onePassSubmit = true;

    /**
     * Do not notify users when a blog is created
     *
     * @var boolean
     */
	protected $_defaultNotification = false;

    /**
     * Whether or not we should create an album when this
     * item is saved
     *
     * @var Boolean
     */
    protected $_createAlbumOnSave = true;

    /**
     * Blogs have simple albums
     *
     * @var string
     */
    protected $_albumType = Media_Album::TYPE_SIMPLE;

    /**
     * The type of album attached to this object:
     * simple or aggregation
     *
     * @var string
     */
    protected $_albumAccess = Media_Album::ACCESS_PUBLIC;
    
	/**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Blog_Form';

    /** 
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::BLOGS;
    
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

	public function getFolderPath()
	{
		throw new Lib_Exception("Blogs have no folders and must not be asked for a folder path");
	}

	public function hasBlogPosts()
	{
		$table = new Blog_Post();
		$select = $table->select();
		$select->where('blogId = '.$this->id);
		$rowset = $select->query()->fetchAll();
		$return = (count($rowset) > 0);
		return $return;
	}

	public function getUser()
	{
		$user = $this->findParentRow('User', 'Submitter');
		return $user;
	}
}