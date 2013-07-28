<?php
class ForumController extends Lib_Controller_Action
{
    protected $_aclActionRules = array(
        'newpost' => array('resource' => Lib_Acl::REGISTERED_RESOURCE ),
        'newtopic' => array('resource' => Lib_Acl::REGISTERED_RESOURCE ),
    );

    /**
     * List of fields in a form that will never match
     * a field in the data DB table.
     * Example: 'submit'
     *
     * @var array
     */
    protected $_disregardUpdates = array(
        'submit',
        'skipAutoFields',
        'token',
        'title',
        'description'
    );

	/**
	 * Whether or not to generate additional content for display
	 *
	 * @var boolean
	 */
    protected $_useAdditionalContent = false;    
    
    /**
     * Initialisations
     */
    public function init()
    {
        parent::init();
        Zend_Registry::set('Category', Category::COMMUNITY);
        Zend_Registry::set('SubCategory', SubCategory::FORUMS);
    }

    /**
     * Lists all forums
     */
    public function indexAction()
    {
        $table = new Forum();
        $forums = $table->fetchAll('status = 1', array('category ASC', 'id ASC'));
        $arrForums = array();
        foreach($forums as $forum){
        	$arrForums[$forum->getId()] = ucfirst($forum->getTitle());
        }
		$this->view->forumListForm = new Forum_List_Form($arrForums, null);
        $this->view->forums = $forums;
        if($identity = Zend_Auth::getInstance()->getIdentity()){
        	$lastLogin = $this->view->lastLogin = $identity->lastLogin;
        } else {
        	$lastLogin = $this->view->lastLogin = null;
        }
    }

    /**
     * Lists all topics within a forum
     *
     */
    public function forumAction()
    {
    	$id = $this->_getParam(2);
        if(empty($id)){
        	throw new Lib_Exception_Forum("No forum given");
        }
    	$table = new Forum();
        $forum = $table->find($id)->current();

        if(!$forum->isReadableBy($this->_user,$this->_acl)){
			$redirector = new Lib_Controller_Helper_RedirectToRoute();
            $redirector->direct('othererror', array('error'=>'unauthorizedPrivateForumRead'), true);
        }

        $dataType = 'Forum_Topic';
        $page = $this->_getParam(3, 1);
        $result = Data_Utils::getChildrenList($this->_user, $this->_acl, $dataType, $forum, 'lastPostDate DESC');
        $topics = $this->_helper->dataPaginator($result['select'], $page, 'commonviews/two-dimension-pagination.phtml', $result['itemsPerPage']);

        $forums = $table->fetchAll('status = 1', array('category ASC', 'id ASC'));
        $arrForums = array();
        foreach($forums as $currentForum){
        	$arrForums[$currentForum->getId()] = ucfirst($currentForum->getTitle());
        }
		$this->view->forumListForm = new Forum_List_Form($arrForums, null);
        $this->view->dataType = $dataType;
        $this->view->forum = $forum;
        $this->view->moderators = $forum->getModerators();
        $this->view->topics = $topics;
        if($identity = Zend_Auth::getInstance()->getIdentity()){
        	$lastLogin = $this->view->lastLogin = $identity->lastLogin;
        } else {
        	$lastLogin = $this->view->lastLogin = null;
        }
    }

    public function gotoforumAction()
    {
    	$id = $this->_request->getParam('goToParameter');
        if(empty($id)){
        	$this->_helper->redirectToRoute('forums');
        }
    	
        $table = new Forum();
    	
    	$result = $table->find($id);    	
        if(empty($result)){
        	$this->_helper->redirectToRoute('forums');
        }
        
        $forum = $result->current();	
        if(empty($forum)){
        	$this->_helper->redirectToRoute('forums');
        }    	
		header("Location: ".$forum->getLink());
		exit();
    }    
    
    /**
     * Creates a new topic within a forum
     *
     */
    public function edittopicAction()
    {
    	$param1 = $this->_getParam(1);
    	$postData = $this->_request->getPost();
    	$topicTable = new Forum_Topic();

    	if(is_numeric($param1)){
    		// Topic editing
    		$action = 'edit';
    		$topicRow = $topicTable->find($param1)->current();
    		$forumRow = $topicRow->getForum();
    		// Only the submitter, moderators, editors and admins can edit a topic
    		if((!$topicRow->isEditableBy($this->_user, $this->_acl)) && !$forumRow->isModeratableBy($this->_user, $this->_acl)){
                $redirector = new Lib_Controller_Helper_RedirectToRoute();
                $redirector->direct('usererror', array('errorCode'=>User::RESOURCE_ACCESS_DENIED), true);
            }

    	} else {
    		// New topic
    		$action = 'new';
    		$topicRow = $topicTable->fetchNew();
    		$topicRow->forumId = $this->_getParam(2);
    		// Not everyone can post a new topic to a private forum
    		if(!$topicRow->isCreatableBy($this->_user, $this->_acl)){
                Globals::getLogger()->security('unauthorizedPrivateForumWrite attempt');
    			$redirector = new Lib_Controller_Helper_RedirectToRoute();
                $redirector->direct('othererror', array('error'=>'unauthorizedPrivateForumWrite'), true);
            }
    	}

		$dbData = $topicRow->toArray();
    	$form = $topicRow->getForm($this->_user, $this->_acl);
        $form->populateFromDatabaseData($dbData);

		if(empty($postData) || !$form->isValid($postData)){
            // Display form because it was not submitted or it has errors
            $this->view->form = $form;
            return;
		}

		if($action == 'edit'){
	    	$this->_updateTopic($topicRow, $form);
       	} else {
			$this->_saveNewTopic($topicRow, $form);
       	}
        $this->_response->setRedirect($topicRow->getLink())
	         ->sendResponse();
    }

    /**
     * Creates new topic and post entries in the database
     * in order to create a new topic
     *
     * @param Forum_Topic_Row $topicRow
     * @param Forum_Topic_Form $form
     * @throws Lib_Exception_Forum
     */
    protected function _saveNewTopic(Forum_Topic_Row $topicRow, Forum_Topic_Form $form)
    {
        $data = $form->getFormattedValuesForDatabase();

        /**
         * @todo: sauver les attributs particuliers du topic (sticky, announcement)
         * et les sortir du tableau data
         */
      	// Update some specific fields of the topic row object:
        $topicRow->title = $data['title'];

      	// Keep the topic invalid while saving it for now:
      	$now = date("Y-m-d H:i:s");
        $topicRow->lastPostDate = $now; 
        $topicRow->lastPoster = $this->_user->{User::COLUMN_USERID}; 
        $topicId = $topicRow->save(true);
        if(empty($topicId)){
            throw new Lib_Exception_Forum("Could not save new topic for forum $topicRow->forumId");
        }

        // Save a new post with all the data from the topic form:
        $postTable = new Forum_Post();
        $postRow = $postTable->fetchNew();
        $postRow->topicId = $topicId;
        $postRow->date = $now;

        // Update of fields
        foreach($data as $key => $value){
            if(in_array($key, $this->_disregardUpdates)){
                continue;
            }
            $postRow->$key = $value;
        }

        // Keep the post invalid while saving it for now:
        $postId = $postRow->save(true);
        if(empty($postId)){
        	// Clean up the topic table
        	$topicRow->delete();
        	throw new Lib_Exception_Forum("Could not save new post for new topic in forum $topicRow->forumId");
        }

       	// Topic and post were saved correctly, we can now validate them
       	$postRow->status = Data::VALID;
       	$postRow->save(false);

        $topicRow->lastPoster = $topicRow->submitter;
        $topicRow->lastPostDate = $topicRow->date;
       	$topicRow->status = Data::VALID;
       	$topicRow->save(false);

        // Update of forum information:
        $forumRow = $topicRow->getForum();
        $forumRow->lastPoster = $topicRow->submitter;
        $forumRow->lastPostDate = $topicRow->date;
        $forumRow->save();
    }

    /**
     * Updates topic and post entries in the database
     * in order to update a topic
     *
     * @param Forum_Topic_Row $topicRow
     * @param Forum_Topic_Form $form
     * @throws Lib_Exception_Forum
     */
    protected function _updateTopic(Forum_Topic_Row $topicRow, Forum_Topic_Form $form)
    {
    	$data = $form->getFormattedValuesForDatabase();
        /**
         * @todo: sauver les attributs particuliers du topic (sticky, announcement)
         * et les sortir du tableau data
         */
      	// Update some specific fields of the topic row object:
        $topicRow->title = $data['title'];

        $postRow = $topicRow->getFirstPost();
        // Update of fields
        foreach($data as $key => $value){
            if(in_array($key, $this->_disregardUpdates)){
                continue;
            }
            $postRow->$key = $value;
        }

       	$postRow->save();
       	$topicRow->save();
    }

    /**
     * Creates a new post or edits an old one
     *
     */
    public function editpostAction()
    {
    	$param1 = $this->_getParam(1);
    	$postData = $this->_request->getPost();
    	$topicTable = new Forum_Topic();
    	$postTable = new Forum_Post();

    	if(is_numeric($param1)){
    		// Post editing
    		$action = 'edit';
    		$postRow = $postTable->find($param1)->current();
    		$topic = $postRow->getTopic();
    		$forumRow = $topic->getForum();

    		if((!$postRow->isEditableBy($this->_user, $this->_acl)) && !$forumRow->isModeratableBy($this->_user, $this->_acl)){
                $redirector = new Lib_Controller_Helper_RedirectToRoute();
                $redirector->direct('usererror', array('errorCode'=>User::RESOURCE_ACCESS_DENIED), true);
            }

    	} else {
    		// New post
    		$action = 'new';
    		$topicId = $this->_getParam(2);
	    	if(empty($topicId)){
	    		throw new Lib_Exception_Forum("No topicId given for post creation");
	    	}
	    	$topic = $topicTable->find($topicId)->current();
    		if(empty($topic)){
	    		throw new Lib_Exception_Forum("Topic $topicId does not exist");
    		}

    		$forumRow = $topic->getForum();
    		if(!$forumRow->checkTopicPostAcces($this->_user)){
                $redirector = new Lib_Controller_Helper_RedirectToRoute();
                $redirector->direct('othererror', array('error'=>'unauthorizedPrivateForumWrite'), true);
            }

    		$postRow = $postTable->fetchNew();
    		$postRow->topicId = $topicId;
    	}

		$dbData = $postRow->toArray();
    	$form = $postRow->getForm($this->_user, $this->_acl);
        $form->populateFromDatabaseData($dbData);

		if(empty($postData) || !$form->isValid($postData)){
            // Display form because it was not submitted or it has errors
            $this->view->form = $form;
            return;
		}

        $this->_savePost($postRow, $form);

    	$topic->lastPoster = $postRow->submitter;
    	$topic->lastPostDate = $postRow->date;
    	$topic->save();

    	$forum = $topic->getForum();
    	$forum->lastPoster = $postRow->submitter;
    	$forum->lastPostDate = $postRow->date;
    	$forum->save();

		if($action == 'edit'){
	    	// Edited post: redirect to the page where the post being edited is
	    	$destination = $postRow->getLink();
	        $this->_response->setRedirect($destination)
	             ->sendResponse();
	        return;
		}

		// New post: redirect to the last page of the topic
	    $destination = $topic->getLastPageLink();
	    $this->_response->setRedirect($destination)
	         ->sendResponse();
    }

    /**
     * Display a topic
     */
    public function topicAction()
    {
    	$id = $this->_getParam(2);
        if(empty($id)){
        	throw new Lib_Exception_Forum("No topic given");
        }
    	$table = new Forum_Topic();
        $topic = $table->find($id)->current();
		$forum = $topic->getForum();

        if(!$forum->isReadableBy($this->_user, $this->_acl) || !$topic->isReadableBy($this->_user, $this->_acl)){
			$redirector = new Lib_Controller_Helper_RedirectToRoute();
            $redirector->direct('othererror', array('error'=>'unauthorizedPrivateForumRead'), true);
        }

        $dataType = 'Forum_Post';

        $page = $this->_getParam(3, 1);
        $result = Data_Utils::getChildrenList($this->_user, $this->_acl, $dataType, $topic, 'date ASC');
        $posts = $this->_helper->dataPaginator($result['select'], $page, 'commonviews/two-dimension-pagination.phtml', $result['itemsPerPage']);

        if(!in_array($this->_user->status, array(User::STATUS_BANNED, User::STATUS_GUEST, User::STATUS_PENDING))){
        	$postTable = new Forum_Post();
        	$newPost = $postTable->fetchNew();
        	$newPost->topic = $topic;
        	$this->view->form = $newPost->getForm($this->_user, $this->_acl);
        }

        $topic->viewBy($this->_user, $this->getRequest());

        if($identity = Zend_Auth::getInstance()->getIdentity()){
        	$lastLogin = $this->view->lastLogin = $identity->lastLogin;
        } else {
        	$lastLogin = $this->view->lastLogin = null;
        }
        $this->view->dataType = $dataType;
        $this->view->topic = $topic;
        $this->view->posts = $posts;
        $this->view->page = $page;
    }

    /**
     * Saves a Forum_Post_Row in database, setting its data from the submitted form
     *
     * @param Forum_Post_Row $postRow
     * @param Forum_Post_Form $form
     * @return int
     * @throws Lib_Exception_Forum
     */
    protected function _savePost(Forum_Post_Row $postRow, Forum_Post_Form $form)
    {
        $data = $form->getFormattedValuesForDatabase();

        // Update of fields
        foreach($data as $key => $value){
            if(in_array($key, $this->_disregardUpdates)){
                continue;
            }
            $postRow->$key = $value;
        }

        // Saving
        $skipAutomaticEditionFields  = $this->_acl->isAllowed($this->_user, Lib_Acl::ADMIN_RESOURCE);
        if(isset($data['skipAutoFields'])){
        	$skipAutomaticEditionFields &= $data['skipAutoFields'];
        }
        $postId = $postRow->save($skipAutomaticEditionFields);
        if(empty($postId)){
            throw new Lib_Exception_Forum("Could not save post");
        }
        return $postId;
    }

}