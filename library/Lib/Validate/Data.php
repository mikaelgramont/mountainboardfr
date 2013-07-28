<?php
abstract class Lib_Validate_Data extends Zend_Validate_Abstract
{
    /**
     * Constraints
     */
    const MUST_EXIST = 'mustExist';
    const MUST_NOT_EXIST = 'mustNotExist';

    const DOES_NOT_EXIST = 'doesNotExist';
    const EXISTS = 'exists';
    const NOTALLOWED = 'notAllowed';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::DOES_NOT_EXIST => "doesNotExist",
        self::EXISTS => "exists"
    );
    /**
     * Applied constraint
     *
     * @var string
     */
    protected $_constraint;
    /**
     * Table
     *
     * @var string
     */
    protected $_table;
    /**
     * Name of the column that hold this data's name
     *
     * @var string
     */
    protected $_titleColumn;

    protected $_emptyAllowed;

    /**
     * Constructor
     *
     * @param string $constraint
     */
    public function __construct($constraint, $table, $titleColumn, $emptyAllowed = false)
    {
        $this->_constraint = $constraint;
        $this->_table = $table;
        $this->_titleColumn = $titleColumn;
        $this->_emptyAllowed = $emptyAllowed;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $found = $this->_findData($value);

        if($this->_constraint == self::MUST_EXIST){
            // Must exist
            if($found){
                return true;
            } else {
                if($this->_emptyAllowed && empty($value)){
                    return true;
                } else {
                    $this->_error(self::DOES_NOT_EXIST);
                    return false;
                }
            }
        } else {
            // Must not exist
            if(!$found){
                return true;
            } else {
                $this->_error(self::EXISTS);
                return false;
            }
        }
    }

    /**
     * Try to find the data element in database
     *
     * @param string $value
     * @return boolean
     */
    protected function _findData($value, $returnValue = false)
    {
        $table = new $this->_table();
        try{
            $where  = $table->getAdapter()->quoteInto('LOWER(`'.$this->_titleColumn.'`) = ?', $value);
            $where .= " AND status > 0";
            $result = $table->fetchRow($where);
        } catch (Exception $e) {
            $logMessage  = "Type: ".get_class($e).PHP_EOL;
            $logMessage .= "Code: ".$e->getCode().PHP_EOL;
            $logMessage .= "Message: ".$e->getMessage().PHP_EOL.$e->getTraceAsString();

            Globals::getLogger()->error($logMessage, Zend_Log::ERR);
        }

        if($returnValue){
        	return $result;
        }

        return !empty($result);
    }
}