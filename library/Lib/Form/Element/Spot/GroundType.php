<?php
class Lib_Form_Element_Spot_GroundType extends Zend_Form_Element_Select
{
    public $name = 'groundType';

    public function __construct($options = null)
    {
        parent::__construct($this->name, $options);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('groundType')))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->setMultiOptions(Spot::$groundTypes);
    }

	public function getHint()
	{
		return 'spotGroundTypeHint';
	}
}