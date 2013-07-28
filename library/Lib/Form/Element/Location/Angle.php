<?php
class Lib_Form_Element_Location_Angle extends Zend_Form_Element_Text
{
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);
        $this->setLabel(ucfirst(Globals::getTranslate()->_($spec)))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->addValidator('Between',true, array(-180, 180));
    }
}