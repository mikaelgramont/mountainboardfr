<?php
/**
 * This class is responsible for all actions
 * directly related to a user:
 * login/logout, user creation and update,
 * account activation, password retrieval operations
 * homepage...
 */
class UserController extends Lib_Controller_Action
{
    const ACTIVATION_KEY_PARAMNAME = 'aK';

    /**
     * List of fields in a form that will never match
     * a field in the user DB table.
     * Example: 'submit'
     *
     * @var array
     */
    private $_disregardUpdates = array(
        'submit',
        User::INPUT_PASSWORD_CONFIRM,
        User::INPUT_PASSWORD_OLD,
        User::INPUT_PASSWORD,
    );

    /**
     * ACL rules parameters for this controler actions
     *
     * @var array
     */
    protected $_aclActionRules = array(
        'homepage'              => array('resource' => Lib_Acl::REGISTERED_RESOURCE),
        'myprofile'             => array('resource' => Lib_Acl::REGISTERED_RESOURCE),
        'profile'             	=> array('resource' => Lib_Acl::REGISTERED_RESOURCE),
    	'update'                => array('resource' => Lib_Acl::PENDING_RESOURCE),
        'logout'                => array('resource' => Lib_Acl::PENDING_RESOURCE),
        'privatemessages'       => array('resource' => Lib_Acl::REGISTERED_RESOURCE),
        'togglePMRead' 			=> array('resource' => Lib_Acl::REGISTERED_RESOURCE),
        'newstuff'       		=> array('resource' => Lib_Acl::REGISTERED_RESOURCE),
        'notifications'       	=> array('resource' => Lib_Acl::REGISTERED_RESOURCE),

        'markallasread'       	=> array('resource' => Lib_Acl::REGISTERED_RESOURCE),

        'login'                 => array('resource' => Lib_Acl::LOGGEDOUTONLY_RESOURCE),
        'openidlogin'           => array('resource' => Lib_Acl::LOGGEDOUTONLY_RESOURCE),
        'openidredirect'        => array('resource' => Lib_Acl::LOGGEDOUTONLY_RESOURCE),
        'lostpassword'          => array('resource' => Lib_Acl::LOGGEDOUTONLY_RESOURCE),
        'activatenewpassword'   => array('resource' => Lib_Acl::LOGGEDOUTONLY_RESOURCE),
        'register'              => array('resource' => Lib_Acl::LOGGEDOUTONLY_RESOURCE, 'params'=>array('errorCode'=>User::ALREADY_REGISTERED)),

        'waitforconfirmation'   => array('resource' => Lib_Acl::LOGGEDOUT_RESOURCE),
        'confirmation'          => array('resource' => Lib_Acl::LOGGEDOUT_RESOURCE),
    );

    public function init()
    {
        parent::init();

        if(in_array($this->_user->{User::COLUMN_STATUS}, array(User::STATUS_BANNED, User::STATUS_PENDING, User::STATUS_GUEST))){
            Zend_Registry::set('Category', Category::ACCOUNT);
        } else {
            Zend_Registry::set('Category', Category::START);
        }
    }

    /**
     * User section home
     *
     */
    public function indexAction()
    {
        Zend_Registry::set('Category', Category::COMMUNITY);
    }

    /**
     * Category page for logged-out users
     * If user is logged in, they are redirected to homepage
     */
    public function accountAction()
    {
    	if(!in_array($this->_user->{User::COLUMN_STATUS}, array(User::STATUS_BANNED, User::STATUS_PENDING, User::STATUS_GUEST))){
    		$this->_forward('homepage');
    		return;
    	}
    	$this->view->registerForm = new User_Form_Register();
    	$this->view->loginForm = new User_Form_Login();
    }

    /**
     * New stuff
     */
    public function newstuffAction()
    {
        Zend_Registry::set('Category', Category::ACCOUNT);
    	Zend_Registry::set('SubCategory', SubCategory::NEWSTUFF);

    	$this->_useAdditionalContent = false;

    	$range = $this->_request->getParam('range');
    	$from = $until = new Zend_Date();
   		$until = $until->get('YYYY-MM-dd HH:mm:ss');
   		$doNotDisplayUntil = true;

   		$useCache = true && ALLOW_CACHE;

   		switch($range){
   			case 'lastDay':
   				$from = $from->subDay(1)->get('YYYY-MM-dd HH:mm:ss');
   				$viewRange = 'overLastDay';
   				break;
   			case 'lastWeek':
   				$from = $from->subWeek(1)->get('YYYY-MM-dd HH:mm:ss');
   				$viewRange = 'overLastWeek';
   				break;
   			case 'lastMonth':
   				$from = $from->subMonth(1)->get('YYYY-MM-dd HH:mm:ss');
   				$viewRange = 'overLastMonth';
   				break;
   			case 'lastVisit':
   			default:
   				$from = Zend_Auth::getInstance()->getIdentity()->lastLogin;
   				if(empty($from)){
   					$from = new Zend_Date();
   					$from = $from->subMonth(1)->get('YYYY-MM-dd HH:mm:ss');
   				}
   				$viewRange = 'sinceLastVisit';
   				break;
   		}

    	if($this->_user->getRoleId() == User::STATUS_ADMIN){
    		$hardFrom = $this->_request->getParam('from');
			if(!empty($hardFrom)){
				$from = $hardFrom;
				$useCache = false;
			}

    		$hardUntil = $this->_request->getParam('until');
			if(!empty($hardUntil)){
				$until = $hardUntil;
				$doNotDisplayUntil = false;
				$useCache = false;
			}

    		$limit = $this->_request->getParam('limit', MAX_NOTIFICATION_ITEMS_ADMIN);
    	} else {
    		$limit = MAX_NOTIFICATION_ITEMS_USERS;

    	}

    	if($useCache){
    		$cache = $this->_user->getCache();
    		$cacheId = Item::getNewItemsCacheId($this->_user->getId(), $viewRange, $limit);
    		$filteredItems = $cache->load($cacheId);
    		if(!$filteredItems){
        		$allNewItems = Item::getAllItemsPostedSince($from, $until, $this->_user, $this->_acl, $limit);
        		$filteredItems = Item::filterOutItems($allNewItems, User_Notification::MEDIUM_HOMEPAGE, $this->_user);
        		$this->_user->getTable()->saveDataInCache($cache, $filteredItems, $cacheId, 200);
    		} else {
    			$filteredItems = Item::wakeupItems($filteredItems);
    		}
    	} else {
        	$allNewItems = Item::getAllItemsPostedSince($from, $until, $this->_user, $this->_acl, $limit);
        	$filteredItems = Item::filterOutItems($allNewItems, User_Notification::MEDIUM_HOMEPAGE, $this->_user);
    	}

		$filteredItems = $this->_addPrivateMessagesToFilteredItems($filteredItems, $from);

        $this->view->rangeForm = new User_Form_GoToNewStuff($range);
        $this->view->range = $viewRange;
        $this->view->from = $from;
        $this->view->until = $doNotDisplayUntil ? null : $until;
        $this->view->items = $filteredItems;
        $this->view->logoutForm = new User_Form_Logout(array(), Globals::getRouter()->assemble(array(), 'logout', true));
    }

    protected function _addPrivateMessagesToFilteredItems($filteredItems, $from)
    {
    	$newMessages = $this->_user->getNewUnreadPrivateMessages($from);
    	foreach($newMessages as $newMessage){
    		$insert = array(
    			'parent' => array(
    				'object' => $newMessage,
    				'dataType' => Constants_DataTypes::PRIVATEMESSAGE,
    			),
    			'children' => array(),
    		);
    		if(!isset($filteredItems['newElementsAndMetadata'])){
    			$filteredItems['newElementsAndMetadata'] = array();
    		}
    		array_unshift($filteredItems['newElementsAndMetadata'], $insert);
    	}

    	$oldMessages = $this->_user->getOldUnreadPrivateMessages($from);
    	foreach($oldMessages as $oldMessage){
    		$insert = array(
    			'parent' => array(
    				'object' => $oldMessage,
    				'dataType' => Constants_DataTypes::PRIVATEMESSAGE,
    			),
    			'children' => array(),
    		);
    		if(!isset($filteredItems['oldElementsAndMetadata'])){
    			$filteredItems['oldElementsAndMetadata'] = array();
    		}
    		array_unshift($filteredItems['oldElementsAndMetadata'], $insert);
    	}

    	return $filteredItems;
    }

    public function gotonewstuffAction()
    {
    	$range = $this->_request->getParam('goToParameter');
   		// No checks here, responsibility is up to the next page
   		$this->_helper->redirectToRoute('newstuff', array('range' => $range));
    }

    public function markallasreadAction()
    {
    	/**
    	 * Mark as read button:
    	 * 	change date of lastLogin in DB to now
    	 *  update in session as well
    	 *  clear all new stuff caches.
    	 */
    	$now = date('Y-m-d H:i:s');
    	$this->_user->lastLogin = $now;
    	$this->_user->save();

    	Zend_Auth::getInstance()->getIdentity()->lastLogin = $now;

    	if($this->_user->getRoleId() == User::STATUS_ADMIN){
    	 	$limit = $this->_request->getParam('limit', MAX_NOTIFICATION_ITEMS_ADMIN);
    	} else {
    		$limit = MAX_NOTIFICATION_ITEMS_USERS;
    	}

    	$cache = $this->_user->getCache();
    	$cacheId = Item::getNewItemsCacheId($this->_user->getId(), 'sinceLastVisit', $limit);
    	$cache->remove($cacheId);
    	$this->_helper->redirectToRoute('newstuff');
    }

    /**
     * User profile page
     * Hidden from the public because we don't want people to be able to figure out
     * who lives where by crossing dpt/country information and maps.
     */
    public function profileAction()
    {
    	Zend_Registry::set('Category', Category::COMMUNITY);
        Zend_Registry::set('SubCategory', SubCategory::USERS);

        $userId = $this->_request->getParam(2, null);
        if(empty($userId)){
            throw new Lib_Exception_NotFound("No user id found for profile page");
        }

        if($userId == $this->_user->{User::COLUMN_USERID}){
        	Zend_Registry::set('Category', Category::ACCOUNT);
			Zend_Registry::set('SubCategory', SubCategory::NONE);
        }

        $table = new User();
        $user = $table->find($userId)->current();
        if(empty($user)){
            throw new Lib_Exception_NotFound("User '$userId' could not be found for profile page");
        }

        $blog = $user->getBlog();
        if(!$blog->isReadableBy($this->_user, $this->_acl)){
            $blog = null;
    	}

        $album = $user->getProfileAlbum();
        if(!$album->isReadableBy($this->_user, $this->_acl)){
            $album = null;
    	}

        $this->view->profilee = $user;
        $this->view->album = $album;
        $this->view->blog = $blog;
        $this->view->medias =  $album->getItemSet();
    }

    /**
     * Logged-in user's profile page
     */
    public function myprofileAction()
    {
    	Zend_Registry::set('Category', Category::ACCOUNT);
    	Zend_Registry::set('SubCategory', SubCategory::NONE);

        $album = $this->_user->getProfileAlbum();
        if(!$album->isReadableBy($this->_user, $this->_acl)){
            $album = null;
    	}

        $blog = $this->_user->getBlog();
        if(!$blog->isReadableBy($this->_user, $this->_acl)){
            $blog = null;
    	}

        $this->view->profilee = $this->_user;
        $this->view->album = $album;
        $this->view->blog = $blog;
        $this->view->medias =  $album->getItemSet();

        $this->renderScript('user/profile.phtml');
    }

    /**
     * Private messages page
     * Reading, sending, replying.
     */
    public function privatemessagesAction()
    {
        $type = $this->_request->getParam('type');
        $page = $this->_request->getParam('page');

        if(!in_array($type, array('home', 'archives', 'sent', 'new', 'reply', 'result'))){
        	throw new Lib_Exception("Unknown action type: '$type'");
        }

    	Zend_Registry::set('Category', Category::ACCOUNT);
    	Zend_Registry::set('SubCategory', SubCategory::PRIVATEMESSAGES);

        $newMessage = $messages = $form = null;
        $table = new PrivateMessage();
        $select = $table->select();
        $select->order('id DESC');
        switch($type){
        	case 'result':
        		$this->view->result = $this->_request->getParam('result');
        		break;
        	case 'home':
        		// Old messages for a given page
        		$select->where('toUser = '.$this->_user->{User::COLUMN_USERID});
        		$messages = $this->_helper->dataPaginator($select, $page, 'commonviews/pagination.phtml', PRIVATE_MESSAGES_MAX_PER_PAGE);
        		break;
        	case 'sent':
        		// Sent messages for a given page
        		$select->where('submitter = '.$this->_user->{User::COLUMN_USERID});
        		$messages = $this->_helper->dataPaginator($select, $page, 'commonviews/pagination.phtml', PRIVATE_MESSAGES_MAX_PER_PAGE);
        		break;
        	case 'new':
        		// Write a new message, to anybody, or maybe to a given user
        		$newMessage = $table->fetchNew();
        		$form = $newMessage->getForm($this->_user, $this->_acl);
		        $data = $this->_request->getPost();
        		$toUser = $this->_request->getParam('toUser');
        		$userTable = new User();
        		if(!$data){
		            // Display default data form
	        		$recipient = $userTable->findByName($toUser);
	        		if($recipient){
	        			$form->toUser->setValue($recipient->{User::COLUMN_USERNAME});
	        		}

        			$this->view->form = $form;
		            $this->view->type = $type;
		            return;
		        }


		        if (!$form->isValid($data)) {
		            // Display form with errors
		            $this->view->form = $form;
		            $this->view->type = $type;
		            return;
		        }

		        $recipient = $userTable->findByName($form->toUser->getValue());
		        $newMessage->title = '';
		        $newMessage->content = $form->content->getValue();
		        $newMessage->submitter = $this->_user->getId();
		        $newMessage->toUser = $recipient->getId();
		        $newMessage->status = Data::VALID;
		        $messageId = $newMessage->save();
		        if($messageId){
					$params = array('result' => 'ok');
		        } else {
					$params = array('result' => 'error');
		        }
		        $route = 'privatemessagesresult';
	            $this->_helper->redirectToRoute($route, $params);
        		break;
        	case 'reply':
        		// Write a new message to the submitter of a message
        		$replyToMessageId = $this->_request->getParam(2);
				$replyToMessage = $table->find($replyToMessageId)->current();
				if(empty($replyToMessage)){
					throw new Lib_Exception_NotFound("Private message '$replyToMessageId' could not be found");
				}

				/**
				 * Since the message id is passed in the url, we need to check whether
				 * the current user may read it or not
				 */
				if(!$replyToMessage->isReadableBy($this->_user, $this->_acl)){
					$this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
				}

				$replyToUser = $replyToMessage->getSubmitter();
        		$newMessage = $table->fetchNew();
        		$form = $newMessage->getForm($this->_user, $this->_acl);
       			$form->toUser->setValue($replyToUser->{User::COLUMN_USERNAME});
        		$this->view->replyToMessage = $replyToMessage;
        		$this->view->replyToUser = $replyToUser;
        		break;
        }

       	$this->view->lastLogin = Zend_Auth::getInstance()->getIdentity()->lastLogin;
		$this->view->messages = $messages;
        $this->view->newMessage = $newMessage;
        $this->view->form = $form;
		$this->view->type = $type;
    }

    // LOGIN ACTIONS
    /**
     * Dedicated loginpage
     */
    public function loginpageAction()
    {
        Zend_Registry::set('SubCategory', SubCategory::LOGINPAGE);
    	$this->view->loginForm = new User_Form_Login();
    }

    /**
     * POST login page
     *
     */
    public function loginAction()
    {
        if(!empty($_POST[User::INPUT_OPENID_IDENTITY ])){
            $this->_forward('openidlogin');
            return;
        }

    	$form = new User_Form_Login();
        if (!$form->isValid($this->_request->getPost())) {
            // No one should get here, unless they typed the url directly
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::INVALID_LOGIN_POST_DATA));
        }
        unset($form);

        $authAdapter = $this->_getPostLoginAdapter($_POST[User::INPUT_USERNAME], $_POST[User::INPUT_PASSWORD]);
        $result = $authAdapter->authenticate();
        if (!$result->isValid()) {
            $this->_onLoginError($result->getMessages(), $result->getCode());
        }

        $userRow = $authAdapter->getResultRowObject(array(User::COLUMN_USERID));

        $userTable = new User();
        $results = $userTable->find($userRow->{User::COLUMN_USERID});
        if(!$results){
        	Globals::getLogger()->loginError('Could not find user \''.$userRow->{User::COLUMN_USERID}.'\'');
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::NO_USER_FOR_GIVEN_IDENTITY));
        }
        $user = $results->current();
        if(!$user){
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::NO_USER_FOR_GIVEN_IDENTITY));
        }

        /**
         * If user chose OpenId as a login method, the password column is empty
         * In that case, conventional login is forbidden
         */
        if($user->{User::COLUMN_PASSWORD} === ''){
            throw new Lib_Exception_Login('Post login attempt while password column is empty for user '.$user->{User::COLUMN_USERID});
        }

        if(!empty($_POST[User::INPUT_REMEMBER])){
            Utils::setCookie(User::COOKIE_MD5, strrev($user->{User::COLUMN_PASSWORD}));
            Utils::setCookie(User::COOKIE_USERNAME, $user->username);
            Utils::setCookie(User::COOKIE_REMEMBER, 1);
        }

        $this->_onLoginSuccess($user);
    }

    /**
     * OpenID (1st loop)
     * Here we only receive the identity before sending it to the provider
     */
    public function openidloginAction()
    {
        if(!OPENID_ACTIVE){
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::OPEN_ID_NOT_ACTIVE));
        }
        if(empty($_POST[User::INPUT_OPENID_IDENTITY ])){
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::MISSING_POST_IDENTITY));
        }

        // 1st loop - will not return after authenticate() if everything went well
        $authAdapter = new Zend_Auth_Adapter_OpenId($_POST[User::INPUT_OPENID_IDENTITY]);
        $result = $authAdapter->authenticate();

        // This should only happen in case of bad OpenId identity:
        $messages = $result->getMessages();
        $code = $result->getCode();
        $message  = "OpenId authentication failed. Code : $code".PHP_EOL;
        $message .= implode($messages, ', ');
        Globals::getLogger()->security($message, Zend_Log::NOTICE);

        $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::BAD_IDENTITY));
    }

    /**
     * OpenID (2nd loop)
     * Redirect from OpenID Provider
     */
    public function openidredirectAction()
    {
        if(!OPENID_ACTIVE){
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::OPEN_ID_NOT_ACTIVE));
        }
        if(empty($_GET[User::INPUT_OPENID_GET_IDENTITY])){
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::MISSING_GET_IDENTITY));
        }

        // 2nd loop
        $authAdapter = new Zend_Auth_Adapter_OpenId();
        $result = $authAdapter->authenticate();
        if (!$result->isValid()) {
            $this->_onLoginError($result->getMessages(), $result->getCode());
        }

        $userTable = new User();
        $where = $userTable->getAdapter()->quoteInto(User::COLUMN_OPENID_IDENTITY . " = ?", $_GET[User::INPUT_OPENID_GET_IDENTITY]);
        $user = $userTable->fetchRow($where);
        if(in_array($user->status, array(
        		User::STATUS_BANNED,
        		User::STATUS_GUEST,
        		User::STATUS_PENDING,
        	))){
            $user = null;
        }
        if(!$user){
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::NO_USER_FOR_GIVEN_IDENTITY));
        }

        $this->_onLoginSuccess($user);
    }

    /**
     * Logout page
     *
     */
    public function logoutAction()
    {
       	$auth = Zend_Auth::getInstance()->hasIdentity();
    	if($auth){
            Zend_Session::forgetMe();
            Zend_Session::destroy();
   	    }
        Utils::deleteCookie(User::COOKIE_MD5);
        Utils::deleteCookie(User::COOKIE_USERNAME);
        Utils::deleteCookie(User::COOKIE_REMEMBER);

   	    session_regenerate_id();

        $this->_helper->redirectToRoute('index');
    }

    /**
     * Display error message after failed login
     *
     */
    public function errorAction()
    {
        $code = $this->_request->getParam('errorCode');
        $this->view->errorCode = $code;
        $this->getResponse()->setRawHeader('HTTP/1.1 500 Internal Server Error');
    }

    // USER REGISTRATION & UPDATE ACTIONS
    /**
     * Take care of:
     *  - user registration form
     *  - user creation
     *  - confirmation email creation and sending
     */
    public function registerAction()
    {
        Zend_Registry::set('SubCategory', SubCategory::USERREGISTER);
    	$form = new User_Form_Register();
        $data = $this->_request->getPost();
        if(!$data){
            // Display empty form
            $this->view->registerForm = $form;
            return;
        }

        if (!$form->isValid($data) || $this->_request->getParam('emailFailure')) {
            // Display form with errors
            $this->view->registerForm = $form;
            $this->view->emailFailure =  $this->_request->getParam('emailFailure', null);
            return;
        }

        // Parameters for email and user creation
        $params = array(
            User::COLUMN_USERNAME => $form->getValue(User::INPUT_USERNAME),
            User::COLUMN_PASSWORD => $form->getValue(User::INPUT_PASSWORD),
            User::COLUMN_EMAIL => $form->getValue(User::INPUT_EMAIL),
            //User::COLUMN_OPENID_IDENTITY => $form->getValue(User::INPUT_OPENID_IDENTITY),
            User::INPUT_AUTH_METHOD => $form->getValue(User::INPUT_AUTH_METHOD),
        );

        try{
            // Create user in database
            $user = $this->_createNewUser($params);
            $userId = $user->{User::COLUMN_USERID};

            $params['activationKey'] = $user->activationKey;
            $params['link'] = APP_URL.Globals::getRouter()->assemble(array(),'userconfirmation');
            $params['link'] .= '?'.User::COLUMN_USERID."=$userId&".self::ACTIVATION_KEY_PARAMNAME."={$user->activationKey}";
            $params['site'] = APP_NAME;
        } catch (Exception $e){
        	$msg = "Error while creating user and/or blog. ".$e->getMessage();
            Globals::getLogger()->registrationError($msg);
        	$userId = null;
        }

        if($userId === null){
            // Redirect to error page in case of user creation failure
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::CREATION_FAILURE));
        }

        try{
            // Send Email
            $emailStatus = $this->_helper->emailer()->sendEmail($params[User::COLUMN_EMAIL], $params);
        } catch (Exception $e) {
            $emailStatus = false;
            $msg = "Email error 2 ".$e->getMessage();
            Globals::getLogger()->registrationError($msg);
        }

        if(!$emailStatus){
            // If there was en error sending the email, delete user row, and forward to current page
            $this->_cleanupUser($userId);
            $this->_forward('register', null, null, array('emailFailure'=>1));
            return;
        }

        // Success !
        $this->_savePendingUserIdentity($userId);
        $this->_helper->redirectToRoute('userpending');
    }

    /**
     * Page displayed while waiting for user to confirm
     * registration via email
     */
    public function pendingAction()
    {
        Zend_Registry::set('SubCategory', SubCategory::USERUPDATE);
    	$user = $this->_user;
        if($user->{User::COLUMN_STATUS} == User::STATUS_MEMBER  ){
            // Redirect to update if is a member already
            $this->_helper->redirectToRoute('userupdate');
        }

        $form = new User_Form_Update($user, true);

        // Populate form with data from DB in case user comes back to this page
        $form->populateFromDatabaseData($user->toArray());

        $data = $this->_request->getPost();
        if(!$data){
            // Display default data form
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($data)) {
            // Display form with errors
            $this->view->form = $form;
            return;
        }

        if(!$this->_updateUser($user, $form->getFormattedValuesForDatabase(), false)){
            // Update failed
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::UPDATE_FAILED));
        }

        // Update succeeded, redirect to next page
        $this->_helper->redirectToRoute('userwaitforconfirmation');
    }

    /**
     * Waiting page. User will receive a confirmation email shortly,
     * but they just can't login yet. Let's display something to keep
     * them busy
     */
    public function waitforconfirmationAction()
    {
    	Zend_Registry::set('SubCategory', SubCategory::USERUPDATE);
    }

    /**
     * Activation of user by click in the confirmation email
     * Display a welcome message on success
     */
    public function confirmationAction()
    {
        Zend_Registry::set('SubCategory', SubCategory::USERUPDATE);
    	$userId = $this->getRequest()->getParam('userId');
        $activationKey = $this->getRequest()->getParam(self::ACTIVATION_KEY_PARAMNAME);

        $user = $this->_getUserFromIdAndKey($userId, $activationKey);

        if(!$user){
            // No such user
            Globals::getLogger()->registrationError("Account activation: user retrieval failed - userId=$userId, key=$activationKey", Zend_Log::INFO );
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::ACTIVATION_FAILED));
        }

        $user->clearCache();
        $user = $this->_getUserFromIdAndKey($userId, $activationKey);

        if($user->{User::COLUMN_STATUS} == User::STATUS_PENDING){
            $user->{User::COLUMN_STATUS} = User::STATUS_MEMBER;
            $user->date = date('Y-m-d H:i:s');
            $id = $user->save();
            if($id !== $userId){
                Globals::getLogger()->registrationError("Account activation: user save failed - userId=$userId, key=$activationKey", Zend_Log::INFO );
                $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::ACTIVATION_FAILED));
            }

            $this->view->success = true;
            $this->view->alreadyDone = false;
        	$this->_savePendingUserIdentity($userId);
        } else {
            $this->view->success = false;
            $this->view->alreadyDone = true;
        }
    }

    /**
     * Profile update page
     *
     */
    public function updateAction()
    {
        Zend_Registry::set('SubCategory', SubCategory::USERUPDATE);

        // Did we just confirm a new password ?
        $this->view->newPassword = $this->getRequest()->getParam('newPassword', null);

        $useOpenId = !empty($this->_user->{User::COLUMN_OPENID_IDENTITY});
        $form = new User_Form_Update($this->_user, false, $useOpenId);

        // Populate form with data from DB in case user comes back to this page
        $form->populateFromDatabaseData($this->_user->toArray());

        $data = $this->_request->getPost();
        if(!$data){
            // Display default data form
            $this->view->form = $form;
            return;
        }

        if(!$form->isUpdateValid($data, $this->_user)) {
            // Display form with errors
            $this->view->form = $form;
            return;
        }

        // Avatar url and file upload
        $this->_updateAvatar($form);

        if($this->_user->lang != $data['lang']){
            Globals::getTranslate($data['lang']);
        }

        $this->_helper->dataSaver()->manageLocation($this->_user, $data);

        // Check for password updates
        $passwordUpdates = false;
        if(!empty($data[User::INPUT_PASSWORD_OLD]) && !empty($data[User::INPUT_PASSWORD])){
            $passwordUpdates = true;
            $this->_user->{User::COLUMN_PASSWORD} = md5($data[User::INPUT_PASSWORD]);
        }

        if(!$this->_updateUser($this->_user, $form->getFormattedValuesForDatabase(), $passwordUpdates)){
            // Update failed
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::UPDATE_FAILED));
        }

        // Update succeeded, redirect to next page
        $this->_helper->redirectToRoute('userupdatesuccess');
    }

    /**
     * Static page after user update
     *
     */
    public function userupdatesuccessAction()
    {
    }

    /**
     *  Generation of a new password
     */
    public function lostpasswordAction()
    {
        $user = $this->_user;

        $form = new User_Form_LostPassword();

        $data = $this->_request->getPost();
        if(!$data){
            // Display empty form
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($data)) {
            // Display form with errors
            $this->view->form = $form;
            $this->view->success = false;
            return;
        }

        // Update user with a new password and a new activation key
        $userTable = new User();
        $where = $userTable->getAdapter()->quoteInto(User::COLUMN_USERNAME .' = ?', $this->_request->getParam(User::INPUT_USERNAME));
        $user = $userTable->fetchRow($where);

        if(empty($user)){
            // No user found
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::NO_SUCH_USER));
        }

        $newPassword = $this->_generateNewPassword();
        $user->newPassword = md5($newPassword);
        $newActivationKey = $user->activationKey = $this->_generateActivationKey();

        $id = $user->save();
        if($id != $user->{User::COLUMN_USERID}){
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::NEWPASSWORD_FAILED));
        }

        $link = APP_URL.Globals::getRouter()->assemble(array(),'activatenewpassword');
        $link .= '?'.User::COLUMN_USERID."=".$user->{User::COLUMN_USERID}."&".self::ACTIVATION_KEY_PARAMNAME."={$user->activationKey}";


        $params = array(
            User::COLUMN_USERNAME => $user->{User::COLUMN_USERNAME},
            User::INPUT_USERID => $user->{User::COLUMN_USERID},
            'activationKey' => $newActivationKey,
            'newPassword' => $newPassword,
            'link' => $link,
            'site' => APP_NAME,
        );

        try{
            $emailStatus = $this->_helper->emailer()->sendEmail($user->{User::COLUMN_EMAIL}, $params, Lib_Controller_Helper_Emailer::LOST_PASSWORD_EMAIL);
        } catch (Exception $e) {
            $emailStatus = false;
        }

        if(!$emailStatus){
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::NEWPASSWORD_EMAIL_FAILED));
        }

        $this->view->success = true;
    }

    /**
     * Activation of new password
     */
    public function activatenewpasswordAction()
    {
        $newKey = $this->getRequest()->getParam(self::ACTIVATION_KEY_PARAMNAME, null);
        $userId = $this->getRequest()->getParam(User::COLUMN_USERID, null);

        $user = $this->_getUserFromIdAndKey($userId, $newKey);
        if(!$user){
            Globals::getLogger()->info("New password activation: user retrieval failed - userId=$userId, key=$newKey", Zend_Log::INFO );
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::NO_SUCH_USER));
        }

        $user->{User::COLUMN_PASSWORD} = $user->newPassword;
        $user->newPassword = '';
        $user->activationKey = '';

        $id = $user->save();
        if($id != $user->{User::COLUMN_USERID}){
            $this->_helper->redirectToRoute('usererror',array('errorCode'=>User::NEWPASSWORD_ACTIVATION_FAILED));
        }

        Utils::deleteCookie(User::COOKIE_MD5);
        Utils::deleteCookie(User::COOKIE_USERNAME);
        Utils::deleteCookie(User::COOKIE_REMEMBER);

        $this->_savePendingUserIdentity($userId);

        $this->_helper->redirectToRoute('userupdate',array('newPassword'=>true));
    }

    /**
     * Saves the destination before logging in
     */
    public function savedestinationforredirectAction()
    {
    	$url = $this->_request->getParam(1);
    	$_SESSION['redirectOnLogin'] = urldecode($url);
    	$this->_forward('login');
    }

    /**
     * Edits and saves the type of notifications the current
     * user wants to see
     *
     */
    public function notificationsAction()
    {
    	$medium = User_Notification::MEDIUM_HOMEPAGE;
    	$notifications = $this->_user->getNotifications($medium);
    	$notifications = $this->_user->addDefaultNotifications($notifications, $medium);

        $form = new User_Notification_Form($notifications);
        $form->populateFromDatabaseData($notifications);

        $data = $this->_request->getPost();
        if(!$data || !$form->isValid($data)){
            // Display errors or empty form
            $this->view->form = $form;
            $this->view->status = null;
            return;
        }

        /**
         * Try to fetch the notification rows in DB for each itemType.
         * Create a blank one if it does not exist, then save it.
         */
        $table = new User_Notification();
        $elements = $form->getElements();
        foreach($elements as $element){
        	$name = $element->getName();
        	if(in_array($name, $this->_disregardUpdates)){
        		continue;
        	}

        	if(!isset($data[$name])){
        		continue;
        	}

        	$row = $notifications[$name];
			$row->notify = $element->getValue();
			$row->save();
        }

        $this->_user->clearCache();
        // Update successful
        $this->view->form = null;
        $this->view->status = true;
    }

	/**
	 * Ajax function to mark a message as read
	 *
	 * @return mixed
	 */
    public function togglepmreadAction()
	{
		$messageId = $this->_request->getParam('messageId');
		if(empty($messageId)){
			return null;
		}

		$messageTable = new PrivateMessage();
		$message = $messageTable->find($messageId)->current();
		if(empty($message)){
			return null;
		}

		if(!$message->isReadableBy($this->_user, $this->_acl)){
			return null;
		}

		$message->read = 1 - $message->read;
		$message->save(true);

		die($message->read);

	}

    // PRIVATE FUNCTIONS
    /**
     * Set global user object, Zend_Auth identity data,
     * update last login date and redirect to homepage
     *
     * @param User_Row $userRow
     */
    private function _onLoginSuccess($user)
    {
        Globals::setUser($user);
        Globals::getTranslate($user->lang);

        $lastLoginColumn = User::COLUMN_LAST_LOGIN;

        // User Data to be stored in Zend_Auth identity
        $userData = new stdClass();
        $userData->{User::COLUMN_USERID} = $user->{User::COLUMN_USERID};
        $userData->sessionId = session_id();
        $userData->lastLogin = $user->$lastLoginColumn;
        Zend_Auth::getInstance()->getStorage()->write($userData);

        // Update last login information in database
        $this->_updateLastLoginDate($user);

        //session_regenerate_id();

        // Redirection
        if(isset($_SESSION['redirectOnLogin'])) {
			$url  = $_SESSION['redirectOnLogin'];
			unset($_SESSION['redirectOnLogin']);
        	$this->_response->setRedirect($url)
                            ->sendResponse();
        	exit();
        }

		$routeName = Globals::getRouter()->getCurrentRouteName();
        if($routeName == 'defaults' ){
			$this->_helper->RedirectToRoute('newstuff');
        }
    }

    /**
     * Log error trace and redirect to error page
     *
     * @param array $messages
     * @param integer $code
     */
    private function _onLoginError($messages, $code)
    {
        // Redirect to login error page
        Globals::getLogger()->security(
            'POST Login error:'.PHP_EOL.
            ' - login: ' . $this->_request->getParam(User::INPUT_USERNAME).PHP_EOL.
            ' - message: '.implode(PHP_EOL, $messages).PHP_EOL.
            ' - code: '.$code
        );
        $params = array('errorCode' => $code);
        $this->_helper->RedirectToRoute('usererror', $params);
    }

    /**
     * Return an authentication adapter for POST login
     *
     * @return Zend_Auth_Adapter_DbTable
     */
    private function _getPostLoginAdapter($identity, $password)
    {
        $authorizedLevels = implode(', ',array(
        	"'".User::STATUS_MEMBER."'",
        	"'".User::STATUS_EDITOR."'",
        	"'".User::STATUS_WRITER."'",
        	"'".User::STATUS_ADMIN."'",
        ));

        $authAdapter = new Zend_Auth_Adapter_DbTable(Globals::getMainDatabase());
        $authAdapter->setTableName(Constants_TableNames::USER)
                    ->setIdentityColumn(User::COLUMN_USERNAME)
                    ->setCredentialColumn(User::COLUMN_PASSWORD);
        $authAdapter->setIdentity($identity)->setCredential($password);
		$authAdapter->setCredentialTreatment("MD5(?) AND ".User::COLUMN_STATUS." IN ({$authorizedLevels})");

        return $authAdapter;
    }

    /**
     * Sets user object to current logged user
     */
    private function _setGlobalsUser($userId)
    {
        $userTable = new User();
        $user = $userTable->find($userId)->current();
        Globals::setUser($user);
    }

    /**
     * Sets user object as a default user
     */
    private function _setDefaultUser()
    {
        $user = User::getDefaultUser();
        Globals::setUser($user);
        Zend_Auth::getInstance()->clearIdentity();
    }

    /**
     * Updates the last login field
     *
     * @param User_Row $user
     */
    private function _updateLastLoginDate(User_Row $user)
    {
        $column = User::COLUMN_LAST_LOGIN ;
        $user->$column = date("Y-m-d H:i:s");
        $user->save();
    }

    /**
     * Create user in database
     *
     * @param array $params
     * @return User_Row
     */
    private function _createNewUser($params)
    {
        $table = new User();
        $user = $table->fetchNew();

        $user->lang = Globals::getTranslate()->getLocale();
        $user->activationKey = $this->_generateActivationKey();

        $user->{User::COLUMN_USERNAME} = $params[User::COLUMN_USERNAME];
        $user->{User::COLUMN_EMAIL} = $params[User::COLUMN_EMAIL];
        $user->{User::COLUMN_STATUS} = User::STATUS_PENDING;

        //if($params[User::INPUT_AUTH_METHOD] == User::LOGIN_AUTHMETHOD_PASSWORD){
            $user->{User::COLUMN_PASSWORD} = md5($params[User::COLUMN_PASSWORD]);
        //} else {
            //$user->{User::COLUMN_OPENID_IDENTITY} = $params[User::INPUT_OPENID_IDENTITY];
        //}

        $user->save();
        return $user;
    }

    /**
     * Delete user row
     *
     * @param integer $userId
     */
    private function _cleanupUser($userId)
    {
        $table = new User();
        $where = $table->getAdapter()->quoteInto(User::COLUMN_USERID .' = ?', $userId);
        $table->delete($where);
    }

    /**
     * Return the user with given userId and activation key
     *
     * @param string $userId
     * @param string $activationKey
     * @return User_Row
     */
    private function _getUserFromIdAndKey($userId, $activationKey)
    {
        $table = new User();
        $where = $table->getAdapter()->quoteInto(User::COLUMN_USERID.' = ?', $userId);
        $where2 = $table->getAdapter()->quoteInto(' AND activationKey = ?', $activationKey);
        $user = $table->fetchRow($where.$where2);

        return $user;
    }

    /**
     * Update a user profile with given data
     *
     * @param User_Row $user
     * @param array $data
     */
    private function _updateUser(User_Row $user, $data, $passwordUpdates)
    {
        $status = false; $error = false;

        foreach($data as $field => $value){
            if(in_array($field, $this->_disregardUpdates)){
                continue;
            }
            if(!in_array($field, User::$updatableFields)){
                Globals::getLogger()->security("Trying to update user field $field with value: $value", Zend_Log::NOTICE);
                continue;
            }
            $user->$field = $value;
        }

        $updatedId = $user->save();
        $status = ($updatedId === $user->{User::COLUMN_USERID});

        if($passwordUpdates && !empty($_COOKIE[User::COOKIE_REMEMBER])){
            Utils::setCookie(User::COOKIE_MD5, strrev($user->{User::COLUMN_PASSWORD}));
        }

        return $status;
    }

    /**
     * Save user identity while waiting for confirmation
     *
     * @param integer $userId
     */
    private function _savePendingUserIdentity($userId)
    {
        $userData = new stdClass();
        $userData->{User::COLUMN_USERID} = $userId;
        $userData->sessionId = session_id();
        $userData->lastLogin = date('Y-m-d H:i:s');
        Zend_Auth::getInstance()->getStorage()->write($userData);
    }

    /**
     * Generate a password
     *
     * @return string
     */
    private function _generateNewPassword()
    {
        $password = Utils::getRandomKey(8);
        return $password;
    }

    /**
     * Generate an activation key
     *
     * @return string
     */
    private function _generateActivationKey()
    {
        $key = Utils::getRandomKey(32);
        return $key;
    }

	private function _updateAvatar(Lib_Form $form)
	{
		$avatarFileElement = $form->avatarFile;
		$avatarFile = $avatarFileElement->getValue();
		$avatarUrlElement = $form->avatarUrl;
		$avatarUrl = $avatarUrlElement->getValue();

		if(empty($avatarFile) && empty($avatarUrl)){
			return;
		}

		$destination = AVATARS_PATH . $this->_user->{User::COLUMN_USERID}.'.jpg';
        if(!empty($avatarFile)){
			$avatarFileElement->addFilter('Rename', array('target' => $destination, 'overwrite' => true));
			if(!$avatarFileElement->receive()) {
				throw new Lib_Exception("An error occured while receiving avatar file '$avatarFile'");
			}
			$form->avatarUrl->setValue($destination);
        } else {
			$fileCopy = @copy($avatarUrl, $destination);
			if($fileCopy === false){
				throw new Lib_Exception("An error occured while writing remote avatar file '$avatarUrl' to '$destination'");
			}

        }

		$avatar = new File_Photo($destination);
		$avatar->renameAfterSubType();
		$destination = $avatar->getFullPath();
		$avatar->limitDimensions(MAX_AVATAR_WIDTH, MAX_AVATAR_HEIGHT);

		// Ugly hack for testing on Windows:
		$destination = str_replace('\\', '/', $destination);
		$form->avatarUrl->setValue('/' . $destination);
	}
}
