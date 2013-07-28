<?php
class Lib_Form_Element_TinyMce extends Lib_Form_Element_Textarea
{
	public $helper = 'TinyMce';

	public function __construct($spec, $options = null)
	{
		if(!is_array($options)){
			$options = array();
		}

		parent::__construct($spec, $options);
	}
}