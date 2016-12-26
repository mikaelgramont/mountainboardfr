<?php
/**
 * Username form element with translation and validators
 * Can use autocompletion, and can also reject values if
 * user exists/does not exist, depending on configuration.
 *
 * Stores and retrieves user id's from database if they exist,
 * otherwise, stores names as strings.
 *
 */
class Lib_Form_Element_Username extends Lib_Form_Element_Data
{
    /**
     * Autocomplete helper
     *
     * @var string
     */
    protected $_autoCompleteHelper = 'autoCompleteUser';
    /**
     * Default element name
     *
     * @var string
     */
    protected $_defaultName = User::INPUT_USERNAME;
    /**
     * Validator
     *
     * @var string
     */
    protected $_validator = 'Lib_Validate_Username';

    /**
     * Table name
     *
     * @var string
     */
    protected $_table = 'User';
    /**
     * Name of the column that hold this data's title
     *
     * @var string
     */
    protected $_titleColumn = User::COLUMN_USERNAME;
    /**
     * Name of the column that hold this data's id
     *
     * @var string
     */
    protected $_idColumn = User::COLUMN_USERID;

    protected $_hint = 'userNameHint';
    
    public function __construct($name = null, $autoComplete = false, $mustExist = false, $mustNotExist = false, $emptyAllowed = false, $options = null, $selfUnallowed = false)
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
            $this->addValidator(new $this->_validator(Lib_Validate_Data::MUST_EXIST, $this->_table, $this->_titleColumn, $emptyAllowed, $selfUnallowed));
        } elseif($mustNotExist) {
            $this->addValidator(new $this->_validator(Lib_Validate_Data::MUST_NOT_EXIST, $this->_table, $this->_titleColumn, $emptyAllowed, $selfUnallowed));
        }

        if($autoComplete){
           $this->helper = $this->_autoCompleteHelper;
        }
        
        $this->_setPlaceholder();
    }
    
    protected function _setPlaceholder()
    {
		$this->placeholder = ucfirst(
		    $this->getTranslator()->_($this->getHint()));
    }

    protected function _getDataByTitle($title)
    {
 		$table = new $this->_table();
        $where  = $table->getAdapter()->quoteInto('LOWER(`'.$this->_titleColumn.'`) = ?', $title);
        $where .= " AND status NOT IN ('" . User::STATUS_BANNED."','".User::STATUS_GUEST."')";
        $result = $table->fetchRow($where);
        return $result;
    }

	public function getHint()
	{
		return $this->_hint;
	}

	public function setHint($hint)
	{
		$this->_hint = $hint;
		$this->_setPlaceholder();
	}
}