<?php
class BlogController extends Lib_Controller_Action
{
    protected $_aclActionRules = array(
    );

    /**
     * Initialisations
     */
    public function init()
    {
        parent::init();
        Zend_Registry::set('Category', Category::COMMUNITY);
    }

    /**
     * Lists blogs that have at least one message
     */
    public function indexAction()
    {
        Zend_Registry::set('SubCategory', SubCategory::BLOGS);
        $this->_useAdditionalContent = true;
        
    	$page = $this->_request->getParam('page');
    	$table = new Blog();
    	$select = $table->getActiveBlogSelect();
    	$activeBlogs = $this->_helper->dataPaginator($select, $page, 'commonviews/pagination.phtml', BLOGS_PER_PAGE);
    	$this->view->activeBlogs = $activeBlogs;
    	$this->_helper->layout->setLayout('two-columns');
    	$this->view->wrapperIsCard = false;
        $this->view->separateFirstContentCardHeader = true;
    }

    /**
     * Page of a given blog
     *
     */
    public function blogAction()
    {

    	$blogId = $this->_request->getParam(2);
        $page = $this->_request->getParam(3);

        $blog = Data::factory($blogId, Blog::ITEM_TYPE);
		if(empty($blog)){
            throw new Lib_Exception_NotFound("Blog '$blogId' not found");
        }

        Zend_Registry::set('Category', $blog->getCategory());
        Zend_Registry::set('SubCategory', $blog->getSubCategory());

        $table = new Blog_Post();
        $select = $table->select();
        $select->where("blogId = $blog->id");

        // Regular users only see valid posts
        if(!$blog->isEditableBy($this->_user, $this->_acl)){
        	$select->where("status = '".Data::VALID."'");
        }
        $select->order("date DESC");

        $posts = $this->_helper->dataPaginator($select, $page, 'commonviews/two-dimension-pagination.phtml', BLOGPOSTS_PER_PAGE);

        $this->view->blog = $blog;
        $this->view->dataType = 'Blog_Post';
        $this->view->posts = $posts;
        
        if($this->_user->getId() == $blog->getSubmitter()->getId()){
        	$this->_useAdditionalContent = false;
        	$this->_helper->layout->setLayout('one-column');
        } else {
        	$this->_useAdditionalContent = true;
        	$this->_helper->layout->setLayout('two-columns');
        	$this->view->wrapperIsCard = true;
        }
        
        
    }
}