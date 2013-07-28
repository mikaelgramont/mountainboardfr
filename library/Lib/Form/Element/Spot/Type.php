<?php
class Lib_Form_Element_Spot_Type extends Zend_Form_Element_Select
{
    public $name = 'spotType';

    public function __construct($options = null)
    {
        parent::__construct($this->name, $options);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('spotType')))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->setMultiOptions(Spot::$spotTypes);
    }

	public function getHint()
	{
		return 'spotTypeHint';
	}
}