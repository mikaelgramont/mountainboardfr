<?php
class Lib_Form_Element_Level extends Zend_Form_Element_Select
{
	const BEGINNER		= 'level_beginner';
	const INTERMEDIATE 	= 'level_intermediate';
	const PRO			= 'level_pro';

	public static $levels = array(
		1 => self::BEGINNER,
		2 => self::INTERMEDIATE,
		3 => self::PRO,
	);

	public function __construct($spec = 'level', $label = 'level', $options = null)
	{
		parent::__construct($spec, $options);

		$inArrayValidator = new Zend_Validate_InArray(array_keys(self::$levels));

        $this->setLabel(ucfirst(Globals::getTranslate()->_($label)))
             ->addValidator($inArrayValidator)
             ->setMultiOptions(self::$levels);
	}
}