<?php
/**
 * Spot form element with translation and validators
 * Can use autocompletion, and can also reject values if
 * spot exists/does not exist, depending on configuration.
 *
 * Stores and retrieves spot id's from database if they exist,
 * otherwise, stores names as strings.
 *
 */
class Lib_Form_Element_Spot extends Lib_Form_Element_Data
{
    /**
     * Autocomplete helper
     *
     * @var string
     */
    protected $_autoCompleteHelper = 'autoCompleteSpot';
    /**
     * Default element name
     *
     * @var string
     */
    protected $_defaultName = 'spot';
    /**
     * Validator
     *
     * @var string
     */
    protected $_validator = 'Lib_Validate_Spot';

    /**
     * Table name
     *
     * @var string
     */
    protected $_table = 'Spot';

	public function getHint()
	{
		return 'spotHint';
	}
}