<?php
class Lib_Form_Element_RideType extends Lib_Form_Element_Multiselect
{
	const FREERIDE	= 'rideType_freeride';
	const FREESTYLE = 'rideType_freestyle';
	const KITE		= 'rideType_kite';

	public static $rideTypes = array(
		1 => self::FREERIDE,
		2 => self::FREESTYLE,
		3 => self::KITE,
	);

	public function __construct($spec = 'rideType', $label = 'rideType', $options = null)
	{
		parent::__construct($spec, $options);

		$inArrayValidator = new Zend_Validate_InArray(array_keys(self::$rideTypes));

        $this->setLabel(ucfirst(Globals::getTranslate()->_($label)))
             ->addValidator($inArrayValidator)
             ->setMultiOptions(self::$rideTypes);
	}
}
