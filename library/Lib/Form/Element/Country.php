<?php
class Lib_Form_Element_Country extends Lib_Form_Element_Data
{
    /**
     * Autocomplete helper
     *
     * @var string
     */
    protected $_autoCompleteHelper = 'autoCompleteCountry';

    /**
     * Default element name
     *
     * @var string
     */
    protected $_defaultName = 'country';
    /**
     * Validator
     *
     * @var string
     */
    protected $_validator = 'Lib_Validate_Country';

    /**
     * Table name
     *
     * @var string
     */
    protected $_table = 'Country';

    public function getFormattedValueForDatabase($value)
    {
        $exp = explode(' - ', $value);
        if(count($exp) > 1){
            // We have a country Id in $exp[0]
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
            // In case a country id was submitted
            $result = $this->_getDataById($title);
        }
        return $result;
    }

    /**
     * Return a Country, given a string that contains either its id or its name
     *
     * @param $string $value
     * @return Country_Row
     */
    public function getCountry($value)
    {
        $table = new Country();
        $id = $this->getFormattedValueForDatabase($value);
        $return = $table->find($id)->current();
        if(empty($return)){
            throw new Lib_Exception_NotFound('Unknown country' . $id);
        }

        return $return;
    }

	public function getHint()
	{
		return 'countryHint';
	}
}


