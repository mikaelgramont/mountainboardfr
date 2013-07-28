<?php
class Lib_Form_Element_Gender extends Zend_Form_Element_Select
{
	const MALE		= 'male';
	const FEMALE 	= 'female';

	public static $genders = array(
		1 => self::MALE,
		2 => self::FEMALE,
	);

	public function __construct($spec = 'gender', $label = 'gender', $options = null)
	{
		parent::__construct($spec, $options);

        $inArrayValidator = new Zend_Validate_InArray(array_keys(self::$genders));

		$this->setLabel(ucfirst(Globals::getTranslate()->_($label)))
             ->addValidator($inArrayValidator)
             ->setMultiOptions(self::$genders);
	}
}