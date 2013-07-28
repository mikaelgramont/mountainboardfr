<?php
/**
 * Media albul form element with translation and validators
 * Can use autocompletion, and can also reject values if
 * spot exists/does not exist, depending on configuration.
 *
 * Stores and retrieves album id's from database if they exist,
 * otherwise, stores names as strings.
 *
 */
class Lib_Form_Element_Album extends Lib_Form_Element_Data
{
    /**
     * Autocomplete helper
     *
     * @var string
     */
    protected $_autoCompleteHelper = 'autoCompleteAlbum';
    /**
     * Default element name
     *
     * @var string
     */
    protected $_defaultName = 'albumId';
    /**
     * Validator
     *
     * @var string
     */
    protected $_validator = 'Lib_Validate_Album';

    /**
     * Whether or not only simple albums are permitted
     *
     * @var boolean
     */
    protected $_onlySimple;
    
    /**
     * Table name
     *
     * @var string
     */
    protected $_table = 'Media_Album';

	public function __construct($name = null, $autoComplete = false, $onlySimple = true, $mustExist = false, $mustNotExist = false, $emptyAllowed = false, $options = null)
	{
		$this->_onlySimple = $onlySimple;
		parent::__construct($name, $autoComplete, $mustExist, $mustNotExist, $emptyAllowed, $options);
		$this->addValidator(new $this->_validator(Lib_Validate_Album::ALBUMTYPENOTALLOWED, $this->_table, $this->_titleColumn, $emptyAllowed));
	}
	
}