<?php
class Lib_Form_Element_OpenId_Validate extends Zend_Validate_Abstract implements Lib_Validate_IAjaxValidator
{
    const OPENID_EXISTS = 'openIdExists';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::OPENID_EXISTS => "already exists"
    );

    protected $_currentUserId = null;

    public function __construct($userId = null)
    {
        if($userId === null && isset($_GET['userId'])){
            $userId = $_GET['userId'];
        } else {
            $user = Globals::getUser();
            if($user){
                $userId = $user->{User::COLUMN_USERID};
            }
        }
        $this->_currentUserId = $userId;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is not an already existing validated user OpenId identity
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $table = new User();
        try{
            $result = $this->_exists($value);
        } catch (Exception $e) {
            $logMessage  = "Type: ".$errors->type.' - '.get_class($e).PHP_EOL;
            $logMessage .= "Code: ".$e->getCode().PHP_EOL;
            $logMessage .= "Message: ".$e->getMessage().PHP_EOL.$e->getTraceAsString();

            Globals::getLogger()->error($logMessage);
        }

        $existsResult = $this->_exists($value);
        if($existsResult && $existsResult->{User::COLUMN_USERID} != $this->_currentUserId){
            $this->_error(self::OPENID_EXISTS);
            return false;
        }
        return true;
    }

    protected function _exists($value)
    {
        $table = new User();
        $where  = $table->getAdapter()->quoteInto('LOWER(`'.User::COLUMN_OPENID_IDENTITY .'`) = ?', $value);
        $where .= " AND ". User::COLUMN_STATUS ." >= ".User::STATUS_MEMBER;
        $result = $table->fetchRow($where);
        return $result;
    }


    public function getAjaxParams()
    {
        $params = array();
        $params['route'] = Globals::getRouter()->assemble(array('format' => 'html'), "isopenidavailable", true);
        $params['userId'] = Globals::getUser()->{User::COLUMN_USERID};
        return $params;
    }
}
