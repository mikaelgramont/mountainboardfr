<?php
class Lib_Form_Element_Dpt extends Lib_Form_Element_Data
{
    /**
     * Autocomplete helper
     *
     * @var string
     */
    protected $_autoCompleteHelper = 'autoCompleteDpt';
    /**
     * Default element name
     *
     * @var string
     */
    protected $_defaultName = 'dpt';
    /**
     * Validator
     *
     * @var string
     */
    protected $_validator = 'Lib_Validate_Dpt';

    
    protected $_hint = 'dptHint';
    /**
     * Table name
     *
     * @var string
     */
    protected $_table = 'Dpt';

    public function getFormattedValueForDatabase($value)
    {
        $exp = explode(' - ', $value);
        if(count($exp) > 1){
            // We have a dpt Id in $exp[0]
            $value = $exp[0];
        }
        $return = parent::getFormattedValueForDatabase($value);
        return $return;
    }

    protected function _getDataByTitle($title)
    {
        $result = null;
        
        $table = new $this->_table();
		$where = $table->getAdapter()->quoteInto("title = ? AND status = '".Data::VALID."'", $title);
		$rowset = $table->fetchAll($where);
        if(!empty($rowset)){
        	$result = $rowset[0];
        }
        
        if(empty($result)){
            // In case a dpt id was submitted
            $result = $this->_getDataById($title);
        }
        return $result;
    }

    /**
     * Return a Dpt, given a string that contains either its id or its name
     *
     * @param $string $value
     * @return Dpt_Row
     */
    public function getDpt($value)
    {
        $table = new Dpt();
        $id = $this->getFormattedValueForDatabase($value);
        $return = $table->find($id)->current();
        if(empty($return)){
            throw new Lib_Exception_NotFound('Unknown dpt' . $id);
        }

        return $return;
    }

	public function getHint()
	{
		return $this->_hint;
	}
	
	public function setHint($hint)
	{
		$this->_hint = $hint;
	}
}