<?php
class Lib_Form_Element_Event_Type extends Zend_Form_Element_Select
{
 	const COMPETITION = 'competition';
 	const SESSION = 'session';
 	const DEMO = 'demo';
	
	public static $types = array(
		'competition'  => self::COMPETITION,
		'session' => self::SESSION,
		'demo' => self::DEMO,
 	);   
	
	public function __construct($options = null)
    {
        parent::__construct('eventType', $options);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('eventType')))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->setMultiOptions(self::$types);
    }
}