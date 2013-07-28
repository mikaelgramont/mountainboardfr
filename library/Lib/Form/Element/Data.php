<?php
/**
 * Data form element with translation and validators
 * Can use autocompletion, and can also reject values if
 * data exists/does not exist, depending on configuration.
 *
 * Stores and retrieves user id's from database if they exist,
 * otherwise, stores names as strings.
 *
 */
abstract class Lib_Form_Element_Data extends Zend_Form_Element_Text
{
    /**
     * Default helper is not an autocomplete helper
     *
     * @var string
     */
    protected $_autoCompleteHelper = 'formText';
    /**
     * Default element name
     *
     * @var string
     */
    protected $_defaultName = 'element';
    /**
     * Validator
     *
     * @var string
     */
    protected $_validator = 'Lib_Validate_Data';

    /**
     * Table name
     *
     * @var string
     */
    protected $_table = 'Data';
    /**
     * Name of the column that hold this data's name
     *
     * @var string
     */
    protected $_titleColumn = 'title';
    /**
     * Name of the column that hold this data's id
     *
     * @var string
     */
    protected $_idColumn = 'id';

    public function __construct($name = null, $autoComplete = false, $mustExist = false, $mustNotExist = false, $emptyAllowed = false, $options = null)
    {
        if($mustExist && $mustNotExist){
            throw new Lib_Exception("Element data cannot be required to both exist and not exist");
        }

        if($name === null){
            $name = $this->_defaultName;
        }

        parent::__construct($name, $options);

        $toLowerFilter = new Zend_Filter_StringToLower();
        $toLowerFilter->setEncoding(APP_PAGE_ENCODING);

        $this->setLabel(ucfirst(Globals::getTranslate()->_($name)));
        if($mustExist){
            $this->addValidator(new $this->_validator(Lib_Validate_Data::MUST_EXIST, $this->_table, $this->_titleColumn, $emptyAllowed));
        } elseif($mustNotExist) {
            $this->addValidator(new $this->_validator(Lib_Validate_Data::MUST_NOT_EXIST, $this->_table, $this->_titleColumn, $emptyAllowed));
        }


        $htmlPurifier = new Lib_Filter_HTMLPurifier();
        $this->addFilter($htmlPurifier)
             ->addFilter($toLowerFilter);

        if($autoComplete){
           $this->helper = $this->_autoCompleteHelper;
        }
    }

    /**
     * Retrieve a data's title whether that data is registered or not
     *
     * @param string $value
     * @return string
     */
    public function getValueFromDatabase($value)
    {
        if(strpos($value, NOREALDATA_MARK) === false){
            if(empty($value)){
                return null;
            }

            $data = $this->_getDataById($value);
            if(empty($data)){
                return null;
            }
            return $data->{$this->_titleColumn};
        }

        $return = str_replace(NOREALDATA_MARK, '', $value);
        return $return;
    }

    /**
     * Decides which type to use (id or title) depending on whether
     * a matching data exists in the database
     *
     * @param string $value
     * @return string
     */
    public function getFormattedValueForDatabase($value)
    {
        if(empty($value)){
        	return null;
        }

    	$data = $this->_getDataByTitle($value);

        if(!empty($data)){
            // A data by this name exists
            return $data->{$this->_idColumn};
        }

        $return = NOREALDATA_MARK.$value;
        return $return;
    }

    protected function _getDataByTitle($title)
    {
    	$table = new $this->_table();
        $itemType = $table->getItemType();

        $translatedTextTable = new Data_TranslatedText();
        $type = Data_Form_Element::TITLE;
		$where = $table->getAdapter()->quoteInto("itemType='$itemType' AND type='$type' AND text = ?", $title);
		$textRowset = $translatedTextTable->fetchAll($where);
        if(count($textRowset) == 0){
        	return null;
        }
        $textRow = $textRowset[0];
        $dataRow = $table->find($textRow->id)->current();
        if(empty($dataRow) || $dataRow->status != Data::VALID){
        	return null;
        }

        return $dataRow;
    }

    protected function _getDataById($id)
    {
        $table = new $this->_table();
        $data = $table->find($id)->current();
        return $data;
    }
}