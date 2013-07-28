<?php
class Lib_Validate_Country extends Lib_Validate_Data
{
    const DOES_NOT_EXIST = 'countryDoesNotExist';
    const EXISTS = 'countryExists';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::DOES_NOT_EXIST => "countryDoesNotExist",
        self::EXISTS => "countryExists"
    );

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

    protected function _findData($value, $returnValue = false)
    {
        $table = new $this->_table();
        try{
            $exp = explode(' - ', $value);
            if(count($exp) > 1 || is_numeric(trim($value))){
                // We have a dpt Id in $exp[0]
                $where  = $table->getAdapter()->quoteInto('id = ?', (int)$exp[0]);
                $where .= " AND status > 0";
                $result = $table->fetchRow($where);
            } else {
                // We only have the dpt title
                $value = strtolower(str_replace('-', '', Utils::cleanString($value)));
                $where  = $table->getAdapter()->quoteInto('simpleTitle = ?', $value);
                $where .= " AND status > 0";
                $result = $table->fetchRow($where);
            }
        } catch (Exception $e) {
            $logMessage  = "Type: ".get_class($e).PHP_EOL;
            $logMessage .= "Code: ".$e->getCode().PHP_EOL;
            $logMessage .= "Message: ".$e->getMessage().PHP_EOL.$e->getTraceAsString();

            Globals::getLogger()->error($logMessage);
        }
        return !empty($result);
    }
}