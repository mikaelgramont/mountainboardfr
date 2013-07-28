<?php
class Lib_Form_Element_Email_Validate extends Zend_Validate_EmailAddress implements Lib_Validate_IAjaxValidator
{
    const EMAIL_INVALID = 'emailInvalid';
    const EMAIL_EXISTS = 'emailExists';
    const IS_EMPTY = 'isEmpty';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::EMAIL_INVALID => "bad email",
        self::EMAIL_EXISTS => "already exists",
        self::IS_EMPTY => "may not be empty"
    );

    protected $_currentUserId = null;
    protected $_forbidExistingEmail = false;

    public function __construct($forbidExistingEmail = false, $userId = null)
    {
        $this->_forbidExistingEmail = $forbidExistingEmail;

        if($userId === null && isset($_GET['userId'])){
            $userId = $_GET['userId'];
        } else {
            $user = Globals::getUser();
            if($user){
                $userId = $user->{User::COLUMN_USERID};
            }
        }
        $this->_currentUserId = $userId;

        parent::__construct($allow = Zend_Validate_Hostname::ALLOW_DNS, false, null);
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is not an already existing username
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $status = parent::isValid($value);

        if(!$status){
            // Override cryptic messages with a global, simple error message
            $this->_messages = array(self::EMAIL_INVALID => $this->getTranslator()->translate(self::EMAIL_INVALID));
            return false;
        }

        if($this->_forbidExistingEmail){
            $existsResult = $this->_exists($value);
            if($existsResult && $existsResult->{User::COLUMN_USERID} != $this->_currentUserId){
                $this->_error(self::EMAIL_EXISTS);
                return false;
            }
        }

        return true;
    }

    protected function _exists($value)
    {
        $table = new User();
        $where = $table->getAdapter()->quoteInto(User::COLUMN_EMAIL.' = ?', $value);
        $where .= "AND ".User::COLUMN_STATUS." = '".User::STATUS_MEMBER
            . "' OR " .User::COLUMN_STATUS." = '".User::STATUS_BANNED."'";
		$result = $table->fetchRow($where);
        return $result;
    }

    public function getAjaxParams()
    {
        $params = array();
        $params['route'] = Globals::getRouter()->assemble(array('format' => 'html'), "isemailavailableandvalid", true);
        $params['userId'] = Globals::getUser()->{User::COLUMN_USERID};
        return $params;
    }
}
