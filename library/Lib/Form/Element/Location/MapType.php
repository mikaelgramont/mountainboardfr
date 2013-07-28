<?php
class Lib_Form_Element_Location_MapType extends Zend_Form_Element_Select
{
    public $name = 'mapType';

    public function __construct($options = null)
    {
        parent::__construct($this->name, $options);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('mapType')))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->setMultiOptions(Location::$mapTypes);
    }
}