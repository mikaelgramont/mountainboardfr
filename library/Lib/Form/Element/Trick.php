<?php
/**
 * Trick form element with translation and validators
 * Can use autocompletion, and can also reject values if
 * trick exists/does not exist, depending on configuration.
 *
 * Stores and retrieves trick id's from database if they exist,
 * otherwise, stores names as strings.
 *
 */
class Lib_Form_Element_Trick extends Lib_Form_Element_Data
{
    /**
     * Autocomplete helper
     *
     * @var string
     */
    protected $_autoCompleteHelper = 'autoCompleteTrick';
    /**
     * Default element name
     *
     * @var string
     */
    protected $_defaultName = 'trick';
    /**
     * Validator
     *
     * @var string
     */
    protected $_validator = 'Lib_Validate_Trick';

    /**
     * Table name
     *
     * @var string
     */
    protected $_table = 'Trick';

	public function getHint()
	{
		return 'trickHint';
	}
}