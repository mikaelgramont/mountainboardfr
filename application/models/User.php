<?php
class User extends Cache_Object
{
    const ITEM_TYPE = 'user';
	const ALBUM_KEYNAME = 'rider';
	const VALID_USER_LIST_CACHE_ID = 'validUsers';

    protected $_name = Constants_TableNames::USER;

    protected $_rowClass = 'User_Row';

    // LOGIN METHODS
    const LOGIN_METHOD_POST = 'LOGIN_METHOD_POST';
    const LOGIN_METHOD_COOKIE = 'LOGIN_METHOD_COOKIE';
    const LOGIN_METHOD_OPENID_POST = 'LOGIN_METHOD_OPENID_POST';
    const LOGIN_METHOD_OPENID_REDIRECT = 'LOGIN_METHOD_OPENID_REDIRECT';

    const LOGIN_AUTHMETHOD_PASSWORD = 'password';
    const LOGIN_AUTHMETHOD_OPENID = 'openid';

    // COOKIES
    const COOKIE_MD5 = 'userI';
    const COOKIE_USERNAME = 'userN';
    const COOKIE_REMEMBER = 'userR';

    // POST INPUT NAMES
    const INPUT_USERID = 'userId';
    const INPUT_USERNAME = 'userN';
    const INPUT_PASSWORD = 'userP';
    const INPUT_PASSWORD_CONFIRM = 'userPC';
    const INPUT_PASSWORD_OLD = 'userPO';
    const INPUT_EMAIL = 'email';
    const INPUT_OPENID_IDENTITY = 'openidIdentity';
    const INPUT_REMEMBER = 'userR';
    const INPUT_LOGIN = 'userLI';
    const INPUT_LOGOUT = 'userLO';
    const INPUT_AUTH_METHOD = 'authMethod';

    // GET INPUT NAMES
    const INPUT_OPENID_GET_MODE = "openid_mode";
    const INPUT_OPENID_GET_MODE_VALUE = "id_res";
    const INPUT_OPENID_GET_IDENTITY = "openid_identity";

    // DATABASE COLUMN NAMES
    const COLUMN_USERID = 'userId';
    const COLUMN_OPENID_IDENTITY = 'openidIdentity';
    const COLUMN_USERNAME = 'username';
    const COLUMN_PASSWORD = 'password';
    const COLUMN_STATUS = 'status';
    const COLUMN_LAST_LOGIN = 'lastLogin';
    const COLUMN_EMAIL = 'email';

    // ERROR CODES
    /**
     *  Pick up after -4 (@see Zend_Auth_Result)
     */
    const INVALID_LOGIN_POST_DATA = -5;
    const OPEN_ID_NOT_ACTIVE = -6;
    const MISSING_POST_IDENTITY = -7;
    const MISSING_GET_IDENTITY = -8;
    const NO_USER_FOR_GIVEN_IDENTITY = -9;
    const DIRECT_REQUEST_NOT_ALLOWED = -10;
    const ALREADY_REGISTERED = -11;
    const CREATION_FAILURE = -12;
    const UPDATE_FAILED = -13;
    const NOT_LOGGED_IN = -14;
    const ACTIVATION_FAILED = -15;
    const LOGGED_IN = -16;
    const NO_SUCH_USER = -17;
    const NEWPASSWORD_FAILED = -18;
    const NEWPASSWORD_EMAIL_FAILED = -19;
    const NEWPASSWORD_ACTIVATION_FAILED = -20;
    const BAD_IDENTITY = -21;
    const RESOURCE_ACCESS_DENIED = -22;

    // USER STATUS AND ACCESS LEVELS
    const STATUS_BANNED = 'banned';
    const STATUS_GUEST = 'guest';
    const STATUS_PENDING = 'pending';
    const STATUS_MEMBER = 'member';
    const STATUS_WRITER = 'writer';
    const STATUS_EDITOR = 'editor';
    const STATUS_ADMIN = 'admin';

    /**
     * List of fields in the User DB table that may
     * be updated directly by submission of a form.
     * This array should not include any 'infrastructure'
     * field such as user id, or creation date, etc.
     *
     * @var array
     */
    public static $updatableFields = array(
        'password',
        'lang',
        'email',
        'openidIdentity',
        'lang',
        'firstName',
        'lastName',
        'birthDate',
        'gender',
        'site',
        'occupation',
        'rideType',
        'level',
        'gear',
        'otherSports',
        'avatar'
    );

    /**
     * Item type
     *
     * @var string
     */
    protected $_itemType = 'user';

    /**
     * Objects that point to this user
     *
     * @var array
     */
    protected $_dependentTables = array('Spot', 'News', 'Article', 'Trick', 'Media', 'User_Notification', 'Blog', 'Facebook_User');

    /**
     * Get the cache for this object
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
    	return Globals::getGlobalCache();
    }

    public function findByName($name)
    {
    	$where = $this->getAdapter()->quoteInto(self::COLUMN_USERNAME.' = ?', $name);
    	$result = $this->fetchRow($where);
    	return $result;
    }

    public function findByEmail($email)
    {
    	$where = $this->getAdapter()->quoteInto(self::COLUMN_EMAIL.' = ?', $email);
    	$result = $this->fetchRow($where);
    	return $result;
    }

    /**
     * Anonymous users get their data from user #0
     *
     * @return Object
     */
    public static function getDefaultUser()
    {
        $userTable = new User_Guest();
        $result = $userTable->fetchRow("status = 'guest'");
        $user = $result;

        if(!($user instanceof User_Guest_Row)){
        	throw new Lib_Exception_User("No guest user found");
        }

        return $user;
    }

    /**
     * If database connexion is down, we set here the default user
     *
     * @return stdClass
     */
    private static function _getDatabaseDownUser()
    {
        $user = new stdClass();
        $user->{User::COLUMN_USERID} = 0;
        $user->username = "Invité";
        return $user;
    }
}