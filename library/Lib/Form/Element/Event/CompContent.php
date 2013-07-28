<?php
class Lib_Form_Element_Event_CompContent extends Lib_Form_Element_Multiselect
{
	const BOARDERCROSS	= 'compContent_boardercross';
	const SLOPESTYLE	= 'compContent_slopestyle';
	const SLALOM	  	= 'compContent_slalom';
	const BIGAIR		= 'compContent_bigair';
	const DOWNHILL		= 'compContent_downhill';
	
	public static $compContent = array(
		1 => self::BOARDERCROSS,
		2 => self::SLOPESTYLE,
		3 => self::SLALOM,
		4 => self::BIGAIR,
		5 => self::DOWNHILL,
	);

	public function __construct($spec = 'compContent', $label = 'compContent', $options = null)
	{
		parent::__construct($spec, $options);

		$inArrayValidator = new Zend_Validate_InArray(array_keys(self::$compContent));

        $this->setLabel(ucfirst(Globals::getTranslate()->_($label)))
             ->addValidator($inArrayValidator)
             ->setMultiOptions(self::$compContent);
	}
}
