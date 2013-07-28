<?php
class Lib_Form_Element_RemoteFile extends Zend_Form_Element_Text
{
	public function __construct($name, $required = true, $options = array())
	{
		// call parent
		parent::__construct($name, $options);
		$this->addPrefixPath('Lib_Filter_File', 'Lib/Filter/File', Zend_Form_Element::FILTER);
	}

	public function receive()
	{
		/**
		 * copy file from the url in getValue to $destination,
		 * which is fetched through the filter
		 */

		$source = $this->getValue();

		return true;
	}

	public function getValue()
	{
		$value = parent::getValue();

		return $value;
	}

}