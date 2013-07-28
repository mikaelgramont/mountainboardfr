<?php
/**
 * Definition of access policy
 *
 */
class Lib_Acl extends Zend_Acl
{
    // RESOURCES

    const LOGGEDOUTONLY_RESOURCE = 'loggedOutOnlyResource';
    const LOGGEDOUT_RESOURCE = 'loggedOutResource';
    const PENDING_RESOURCE = 'pendingResource';
    const PENDINGONLY_RESOURCE = 'pendingOnlyResource';
    const REGULAR_RESOURCE = 'regularResource';
    const REGISTERED_RESOURCE = 'registeredResource';
    const WRITER_RESOURCE = 'writerResource';
    const EDITOR_RESOURCE = 'editorResource';
    const ADMIN_RESOURCE = 'adminResource';

    const PUBLIC_READ_RESOURCE = 'publicReadResource';
    const PUBLIC_CREATE_RESOURCE = 'publicCreateResource';
    const PUBLIC_EDIT_RESOURCE = 'publicEditResource';

    const PRIVATE_READ_RESOURCE = 'privateReadResource';
    const PRIVATE_CREATE_RESOURCE = 'privateCreateResource';
    const PRIVATE_EDIT_RESOURCE = 'privateEditResource';

    const FORUM_PRIVATE_READ_RESOURCE = 'forumPrivateReadResource';
    const FORUM_PRIVATE_POST_RESOURCE = 'forumPrivatePostResource';

    const FORUM_PUBLIC_READ_RESOURCE = 'forumPublicReadResource';
    const FORUM_PUBLIC_POST_RESOURCE = 'forumPublicPostResource';

    const FORUM_MODERATE_RESOURCE = 'forumModerateResource';

    // ROLES
    const OWNER_ROLE = 'ownerRole';

    const FORUM_PUBLIC_READ_ROLE = 'forumPublicReadRole';
    const FORUM_PUBLIC_POST_ROLE = 'forumPublicPostRole';

    const FORUM_PRIVATE_READ_ROLE = 'forumPrivateReadRole';
    const FORUM_PRIVATE_POST_ROLE = 'forumPrivatePostRole';

    const FORUM_MODERATE_ROLE = 'forumModerateRole';

    protected $_user;

    protected $_defaultControllerAccessErrorRoute = 'usererror';
    protected $_defaultControllerAccessErrorParams = array('errorCode'=>User::RESOURCE_ACCESS_DENIED);

    protected $_defaultActionAccessErrorRoute = 'usererror';
    protected $_defaultActionAccessErrorParams = array('errorCode'=>User::RESOURCE_ACCESS_DENIED);

    /**
     * Constructor
     * Acces policy is defined here
     *
     * @param User_Row $user
     */
    public function __construct(User_Row $user)
    {
        $this->_user = $user;
        $this->_setupRoles();
        $this->_setupResources();

		/**
		 * PERMISSIONS AND DENIALS
		 */
		$this->deny();
		//return;

        // Banned users
        $this->deny(User::STATUS_BANNED);

        // Guests
        $this->allow(User::STATUS_GUEST, self::LOGGEDOUTONLY_RESOURCE);
        $this->allow(User::STATUS_GUEST, self::LOGGEDOUT_RESOURCE);
        $this->allow(User::STATUS_GUEST, self::REGULAR_RESOURCE );
        $this->allow(User::STATUS_GUEST, self::PUBLIC_READ_RESOURCE );
        $this->allow(User::STATUS_GUEST, self::FORUM_PUBLIC_READ_RESOURCE );

        // Pending members
        $this->deny(User::STATUS_PENDING, self::LOGGEDOUTONLY_RESOURCE);
        $this->allow(User::STATUS_PENDING, self::PENDING_RESOURCE);
        $this->allow(User::STATUS_PENDING, self::PENDINGONLY_RESOURCE);

        // Members
        $this->deny(User::STATUS_MEMBER, self::LOGGEDOUT_RESOURCE);
        $this->deny(User::STATUS_MEMBER, self::PENDINGONLY_RESOURCE);
        $this->allow(User::STATUS_MEMBER, self::REGISTERED_RESOURCE);
        $this->allow(User::STATUS_MEMBER, self::PUBLIC_CREATE_RESOURCE);
        $this->allow(User::STATUS_MEMBER, self::FORUM_PUBLIC_READ_RESOURCE);
        $this->allow(User::STATUS_MEMBER, self::FORUM_PUBLIC_POST_RESOURCE);

        // Writers
        $this->allow(User::STATUS_WRITER, self::WRITER_RESOURCE);

        // Editors
        $this->allow(User::STATUS_EDITOR, self::EDITOR_RESOURCE);
        $this->allow(User::STATUS_EDITOR, self::PUBLIC_EDIT_RESOURCE);
        $this->allow(User::STATUS_EDITOR, self::FORUM_PRIVATE_READ_RESOURCE);
        $this->allow(User::STATUS_EDITOR, self::FORUM_PRIVATE_POST_RESOURCE);
        $this->allow(User::STATUS_EDITOR, self::FORUM_MODERATE_RESOURCE);

        // Admins
        $this->allow(User::STATUS_ADMIN, self::ADMIN_RESOURCE);

        // Current user can edit their own public resources and have all access to their private resources
        if($this->_user->{User::COLUMN_USERID} > 0){
            $ownerRole = $this->_user->getOwnerRole();
            $this->allow($ownerRole, self::PUBLIC_READ_RESOURCE . '_' . $this->_user->{User::COLUMN_USERID});
            $this->allow($ownerRole, self::PUBLIC_EDIT_RESOURCE . '_' . $this->_user->{User::COLUMN_USERID});
            $this->allow($ownerRole, self::PRIVATE_READ_RESOURCE . '_' . $this->_user->{User::COLUMN_USERID});
            $this->allow($ownerRole, self::PRIVATE_CREATE_RESOURCE . '_' . $this->_user->{User::COLUMN_USERID});
            $this->allow($ownerRole, self::PRIVATE_EDIT_RESOURCE . '_' . $this->_user->{User::COLUMN_USERID});
        }

    }

    protected function _setupRoles()
    {
        $this->addRole(new Zend_Acl_Role(User::STATUS_BANNED));

        $this->addRole(new Zend_Acl_Role(User::STATUS_GUEST));
        $this->addRole(new Zend_Acl_Role(User::STATUS_PENDING), User::STATUS_GUEST);
        $this->addRole(new Zend_Acl_Role(User::STATUS_MEMBER), User::STATUS_PENDING);
        $this->addRole(new Zend_Acl_Role(User::STATUS_WRITER), User::STATUS_MEMBER);
        $this->addRole(new Zend_Acl_Role(User::STATUS_EDITOR), User::STATUS_WRITER);
        $this->addRole(new Zend_Acl_Role(User::STATUS_ADMIN), User::STATUS_EDITOR);

        $this->addRole($this->_user->getOwnerRole());

        $this->addRole(new Zend_Acl_Role(self::FORUM_PUBLIC_READ_ROLE));
        $this->addRole(new Zend_Acl_Role(self::FORUM_PUBLIC_POST_ROLE), self::FORUM_PUBLIC_READ_ROLE);

        $this->addRole(new Zend_Acl_Role(self::FORUM_PRIVATE_READ_ROLE));
        $this->addRole(new Zend_Acl_Role(self::FORUM_PRIVATE_POST_ROLE), self::FORUM_PRIVATE_READ_ROLE);

        $this->addRole(new Zend_Acl_Role(self::FORUM_MODERATE_ROLE));
    }

    protected function _setupResources()
    {
        /**
         * GENERAL RESOURCES
         */
        $this->add(new Zend_Acl_Resource(self::LOGGEDOUT_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::LOGGEDOUTONLY_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::PENDING_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::PENDINGONLY_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::REGULAR_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::REGISTERED_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::WRITER_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::EDITOR_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::ADMIN_RESOURCE));

        /**
         * PUBLIC RESOURCES
         */
        $this->add(new Zend_Acl_Resource(self::PUBLIC_READ_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::PUBLIC_CREATE_RESOURCE, self::PUBLIC_READ_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::PUBLIC_EDIT_RESOURCE, self::PUBLIC_CREATE_RESOURCE));

        /**
         * PRIVATE RESOURCES
         */
        $this->add(new Zend_Acl_Resource(self::PRIVATE_READ_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::PRIVATE_CREATE_RESOURCE, self::PRIVATE_READ_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::PRIVATE_EDIT_RESOURCE, self::PRIVATE_CREATE_RESOURCE));

        /**
         * OWNER RESOURCES
         */
        $this->add(new Zend_Acl_Resource(self::PUBLIC_READ_RESOURCE . '_' . $this->_user->{User::COLUMN_USERID}));
        $this->add(new Zend_Acl_Resource(self::PUBLIC_EDIT_RESOURCE . '_' . $this->_user->{User::COLUMN_USERID}));
        $this->add(new Zend_Acl_Resource(self::PRIVATE_READ_RESOURCE . '_' . $this->_user->{User::COLUMN_USERID}));
        $this->add(new Zend_Acl_Resource(self::PRIVATE_CREATE_RESOURCE . '_' . $this->_user->{User::COLUMN_USERID}));
        $this->add(new Zend_Acl_Resource(self::PRIVATE_EDIT_RESOURCE . '_' . $this->_user->{User::COLUMN_USERID}));


        /**
         * FORUM RESOURCES
         */
        $this->add(new Zend_Acl_Resource(self::FORUM_PRIVATE_READ_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::FORUM_PRIVATE_POST_RESOURCE, self::FORUM_PRIVATE_READ_RESOURCE));

        $this->add(new Zend_Acl_Resource(self::FORUM_PUBLIC_READ_RESOURCE));
        $this->add(new Zend_Acl_Resource(self::FORUM_PUBLIC_POST_RESOURCE, self::FORUM_PUBLIC_READ_RESOURCE));

        $this->add(new Zend_Acl_Resource(self::FORUM_MODERATE_RESOURCE));
    }
    /**
     * Checks whether access should be granted to the current controller,
     * and redirect if not.
     *
     * @param array $rule
     * @param Zend_Controller_Request_Abstract $request
     * @throws Lib_Exception
     * @return void
     */
    public function checkControllerAccess(array $rule, Zend_Controller_Request_Abstract $request)
    {
        if(empty($rule)){
            // No controller-wide rule to enforce in the current controller, access is granted
            return;
        }

        $controllerName = $request->getControllerName();
        if(!array_key_exists('resource', $rule)){
            throw new Lib_Exception('No resource defined for the acl rule of controller '.$controllerName);
        }
        if($this->isAllowed($this->_user->getRoleId(), new Zend_Acl_Resource($rule['resource']))){
            // Access is granted
            return;
        }

        /**
         * ERROR: ACCESS IS DENIED
         */
        // Redirect route
        if(array_key_exists('onErrorGoToRoute', $rule)){
            $route = $rule['onErrorGoToRoute'];
        } else {
            switch($rule['resource']){
                default:
                    $route = $this->_defaultControllerAccessErrorRoute;
                    break;
            }
        }

        // Redirect parameters
        if(array_key_exists('params', $rule)){
            $params = $rule['params'];
        } else {
            switch($rule['resource']){
                case Lib_Acl::LOGGEDOUT_RESOURCE:
                    $params = array('errorCode'=>User::LOGGED_IN);
                    break;
                case Lib_Acl::PENDINGONLY_RESOURCE:
                    $params = array('errorCode'=>User::ALREADY_REGISTERED);
                    break;
                default:
                    $params = $this->_defaultControllerAccessErrorParams;
                    break;
            }
        }

        $this->_handleError($route, $params);
    }

    /**
     * Checks whether access should be granted to the current action,
     * and redirect if not.
     *
     * @param array $aclActionRules
     * @param Zend_Controller_Request_Abstract $request
     * @throws Lib_Exception
     * @return void
     */
    public function checkActionAccess(array $aclActionRules, Zend_Controller_Request_Abstract $request)
    {
        if(empty($aclActionRules)){
            // No rules to enforce in the current controller, access is granted
            return;
        }

        $actionName = $request->getActionName();
        if(!array_key_exists($actionName, $aclActionRules)){
            // No rule to enforce for the current action
            return;
        }

        $rule = $aclActionRules[$actionName];
        if(!array_key_exists('resource', $rule)){
            throw new Lib_Exception('No resource defined for the acl rule of action '.$actionName);
        }
        if($this->isAllowed($this->_user->getRoleId(), new Zend_Acl_Resource($rule['resource']))){
            // Access is granted directly
            return;
        }

        /**
         * ERROR: ACCESS IS DENIED
         */
        // Redirect route
        if(array_key_exists('onErrorGoToRoute', $rule)){
            $route = $rule['onErrorGoToRoute'];
        } else {
            switch($rule['resource']){
                default:
                    $route = $this->_defaultActionAccessErrorRoute;
                    break;
            }
        }

        // Redirect parameters
        if(array_key_exists('params', $rule)){
            $params = $rule['params'];
        } else {
            switch($rule['resource']){
                case Lib_Acl::LOGGEDOUT_RESOURCE:
                    $params = array('errorCode'=>User::LOGGED_IN);
                    break;
                case Lib_Acl::PENDINGONLY_RESOURCE:
                    $params = array('errorCode'=>User::ALREADY_REGISTERED);
                    break;
                default:
                    $params = $this->_defaultActionAccessErrorParams;
                    break;
            }
        }

        $this->_handleError($route, $params);
    }

    /**
     * Redirect to error page
     *
     * @param string $route
     * @param array $params
     * @return void
     */
    protected function _handleError($route, $params = array())
    {
        $redirector = new Lib_Controller_Helper_RedirectToRoute();
        $redirector->direct($route, $params, true);
    }

}