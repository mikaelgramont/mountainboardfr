<?php
/**
 * This plugin is responsible for automatic COOKIE login,
 * and for the detection of the 2nd OpenID loop.
 * - cookie login: user is logged in here automatically and the request
 *   is dispatched normally
 * - 2nd OpenID loop: request is overwritten with the necessary one,
 *   and dispatched. If everything works fine, user is redirected to
 *   their homepage as usual.
 */
class Lib_Plugin_Init_User extends Zend_Controller_Plugin_Abstract
{
    protected $_request;

    /**
     * Performs cookie login operations
     * and sets up User object
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
    	if($this->_isLoggedIn()) {
        	// Previously logged in, during the same session
            $user = $this->_setUser();
            return $user;
        }
        // Is user logging in ? If yes, how ?
        $loginRequestInformation = $this->_loginRequest();

        if(!$loginRequestInformation['status']){
            // Default user
            $user = $this->_setDefaultUser();
            return $user;
        }

        if($loginRequestInformation['type'] == User::LOGIN_METHOD_OPENID_REDIRECT){
            // OpenID redirect: we'll take care of it in the user controller
            $user = $this->_setDefaultUser();
            $this->_request->setControllerName('user');
            $this->_request->setActionName('openidredirect');
            return $user;
        }

        // Login request
        if($this->_doLogin($loginRequestInformation['type'])){
            // Successful login
            $this->_onLoginSuccess($loginRequestInformation['type']);
        } else {
            // Login error
            $this->_onLoginFail($loginRequestInformation['type']);
        }
    }

    // LOGIN FUNCTIONS

    /**
     * Determines if user is logged in
     *
     * @return boolean
     */
    private function _isLoggedIn()
    {
        $hasIdentity = Zend_Auth::getInstance()->hasIdentity();
        return $hasIdentity;
    }

    /**
     * Returns information about whether user is trying to login, and the method used
     *
     * @return array
     */
    private function _loginRequest()
    {
        $cookieLogin = $this->_request->getCookie(User::COOKIE_MD5) &&
                       $this->_request->getCookie(User::COOKIE_USERNAME) &&
                       $this->_request->getCookie(User::COOKIE_REMEMBER);

        $openIDRedirectFromProvider = isset($_GET[User::INPUT_OPENID_GET_MODE]) &&
                                      $_GET[User::INPUT_OPENID_GET_MODE] == User::INPUT_OPENID_GET_MODE_VALUE &&
                                      isset($_GET[User::INPUT_OPENID_GET_IDENTITY]) &&
                                      OPENID_ACTIVE;

        $status = $cookieLogin || $openIDRedirectFromProvider;

        if($cookieLogin){
            $type = User::LOGIN_METHOD_COOKIE;
        } elseif($openIDRedirectFromProvider){
            $type = User::LOGIN_METHOD_OPENID_REDIRECT;
        } else {
            $type = null;
        }

        return array('status'=>$status,'type'=>$type);
    }

    /**
     * Creates and returns an AuthAdapter for the
     * current login method.
     * Only supports cookie login for now
     *
     * @return Zend_Auth_Adapter
     */
    private function _getLoginAuthAdapter($loginMethod)
    {
        $authorizedLevels = implode(', ',array(
        	"'".User::STATUS_MEMBER."'",
        	"'".User::STATUS_EDITOR."'",
        	"'".User::STATUS_WRITER."'",
        	"'".User::STATUS_ADMIN."'",
        ));

        switch($loginMethod){
            case User::LOGIN_METHOD_COOKIE :
                $authAdapter = new Zend_Auth_Adapter_DbTable(Globals::getMainDatabase());
                $authAdapter->setTableName(Constants_TableNames::USER)
                            ->setIdentityColumn(User::COLUMN_USERNAME)
                            ->setCredentialColumn(User::COLUMN_PASSWORD);
                // Just for a little bit more security, we store the password MD5 in reverse
                $authAdapter->setIdentity($this->_request->getCookie(User::COOKIE_USERNAME))
                            ->setCredential(strrev($this->_request->getCookie(User::COOKIE_MD5)));
                $authAdapter->setCredentialTreatment(User::COLUMN_STATUS." IN ({$authorizedLevels})");
                break;
            default:
                throw new Exception_Login("Unknown login method: {$loginMethod}", Lib_Exception_Login::UNKNOWN_METHOD);
                break;
        }

        return $authAdapter;
    }

    /**
     * Performs login actions
     * @param string $loginMethod
     * @return boolean
     */
    private function _doLogin($loginMethod)
    {
        $return = false;

        $authAdapter = $this->_getLoginAuthAdapter($loginMethod);
        $result = $authAdapter->authenticate();

        if (!$result->isValid()) {
            // Login failed
            return false;
        }

        // Login successful
        switch($loginMethod){
            case User::LOGIN_METHOD_COOKIE :
                $userIdColumn = User::COLUMN_USERID;
                $lastLoginColumn = User::COLUMN_LAST_LOGIN;
                $user = $authAdapter->getResultRowObject(array($userIdColumn, $lastLoginColumn));
                $userData = new stdClass();
                $userData->session_id = session_id();
                $userData->$userIdColumn = $user->$userIdColumn;
                $userData->lastLogin = $user->$lastLoginColumn;
                Zend_Auth::getInstance()->getStorage()->write($userData);
                return true;
                break;
        }

        return false;
    }

    /**
     * Perform actions after a successful login attempt
     *
     * @param string $loginMethod
     */
    private function _onLoginSuccess($loginMethod)
    {
        $user = $this->_setUser();

        /**
         * If user chose OpenId as the main login method, the password column is empty.
         * In that case, cookie login is forbidden
         */
        if($loginMethod == User::LOGIN_METHOD_COOKIE && $user->{User::COLUMN_PASSWORD} === ''){
            throw new Lib_Exception_Login('Cookie login attempt while password column is empty for user '.$user->{User::COLUMN_USERID});
        }

        $lastLoginColumn = User::COLUMN_LAST_LOGIN;
        $user->$lastLoginColumn = date("Y-m-d H:i:s");
        $user->save();

        // Sets or deletes identification cookies

        $remember = $this->_request->getCookie(User::INPUT_REMEMBER);
        if(!empty($remember)){
            // We store the password MD5 in reverse
            Utils::setCookie(User::COOKIE_MD5, strrev($user->password));
            Utils::setCookie(User::COOKIE_USERNAME, $user->username);
            Utils::setCookie(User::COOKIE_REMEMBER, 1);

        } else {
            Utils::deleteCookie(User::COOKIE_MD5);
            Utils::deleteCookie(User::COOKIE_USERNAME);
            Utils::deleteCookie(User::COOKIE_REMEMBER);
        }

	// Do not regenerate session id since that causes an issue with memcache.
	// https://bugs.php.net/bug.php?id=71187
        // session_regenerate_id();

        $routeName = Globals::getRouter()->getCurrentRouteName();
        
        /**
         * After logging in, if the page we are loading is the index page,
         * show new stuff instead. This allows for a user to load a specific
         * page on the website by clicking on an external link, and it also
         * allows users to see new stuff by loading the home page 'defaults'
         */
        if($routeName == 'defaults' ){
            $helper = new Lib_Controller_Helper_RedirectToRoute();
            $helper->direct('newstuff');
        }
    }

    /**
     * Performs cleanup actions after a failed login attempt
     *
     * @param string $loginMethod
     */
    private function _onLoginFail($loginMethod)
    {
        $this->_setDefaultUser();

        Utils::deleteCookie(User::COOKIE_MD5);
        Utils::deleteCookie(User::COOKIE_USERNAME);
        Utils::deleteCookie(User::COOKIE_REMEMBER);
    }

    // USER DEFINITION FUNCTIONS

    /**
     * Sets user object to current logged user
     */
    private function _setUser()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $userTable = new User();
        $column = User::COLUMN_USERID;
        if(!isset($identity->$column)){
            $user = User::getDefaultUser();
            Globals::setUser($user);
            return;
        }

        $user = $userTable->find($identity->$column)->current();
        if(!$user){
            $user = User::getDefaultUser();
        }
        Globals::setUser($user);
        
        return $user;
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
}
