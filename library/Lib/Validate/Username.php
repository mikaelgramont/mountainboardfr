<?php
class Lib_Validate_Username extends Lib_Validate_Data
{
    const SELF_UNALLOWED = "selfUnallowed";
	/**
     * @var array
     */
    protected $_messageTemplates = array(
        Lib_Validate_Data::DOES_NOT_EXIST => "usernameDoesNotExist",
        Lib_Validate_Data::EXISTS => "usernameExists",
        self::SELF_UNALLOWED => "selfUnallowed"
    );

    protected $_selfUnallowed;

    /**
     * Constructor
     *
     * @param string $constraint
     */
    public function __construct($constraint, $table, $titleColumn, $emptyAllowed = false, $selfUnallowed = false)
    {
    	parent::__construct($constraint, $table, $titleColumn, $emptyAllowed);
    	$this->_selfUnallowed = $selfUnallowed;
    }

	/**
     * Defined by Zend_Validate_Interface
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
    	$status = parent::isValid($value);
    	if(!$status){
    		return false;
    	}

    	if(!$this->_selfUnallowed){
    		return true;
    	}

    	$user = $this->_findData($value,true);
    	$currentUserId = Globals::getUser()->{User::COLUMN_USERID};
    	if($currentUserId == $user->{User::COLUMN_USERID}){
    		$this->_error(self::SELF_UNALLOWED);
    		return false;
    	}
    	return true;
    }

    /**
     * Try to find the username in database
     *
     * @param string $value
     * @return boolean
     */
    protected function _findData($value, $returnValue = false)
    {
        if(is_string($this->_table)){
        	$table = new $this->_table();
        } else {
        	$table = $this->_table;
        }

        try{
            $where  = $table->getAdapter()->quoteInto('LOWER(`'.$this->_titleColumn.'`) = ?', $value);
            $where .= " AND ". User::COLUMN_STATUS ." IN ('".implode("', '", array(User::STATUS_MEMBER, User::STATUS_WRITER, User::STATUS_EDITOR, User::STATUS_ADMIN))."')";
            $result = $table->fetchRow($where);
        } catch (Exception $e) {
            $logMessage  = "Type: ".get_class($e).PHP_EOL;
            $logMessage .= "Code: ".$e->getCode().PHP_EOL;
            $logMessage .= "Message: ".$e->getMessage().PHP_EOL.$e->getTraceAsString();

            Globals::getLogger()->error($logMessage);
        }

        if($returnValue){
        	return $result;
        }

        return !empty($result);
    }
}