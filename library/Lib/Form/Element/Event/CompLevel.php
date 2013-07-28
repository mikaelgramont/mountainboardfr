<?php
class Lib_Form_Element_Event_CompLevel extends Lib_Form_Element_Multiselect
{
	const NATIONAL	  = 'compLevel_national';
	const WORLDSERIES = 'compLevel_worldSeries';
	const EUROPEAN	  = 'compLevel_european';

	public static $compLevel = array(
		1 => self::NATIONAL,
		2 => self::WORLDSERIES,
		3 => self::EUROPEAN,
	);

	public function __construct($spec = 'compLevel', $label = 'compLevel', $options = null)
	{
		parent::__construct($spec, $options);

		$inArrayValidator = new Zend_Validate_InArray(array_keys(self::$compLevel));

        $this->setLabel(ucfirst(Globals::getTranslate()->_($label)))
             ->addValidator($inArrayValidator)
             ->setMultiOptions(self::$compLevel);
	}
}
