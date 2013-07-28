<?php
class Lib_Form_Element_Tone extends Zend_Form_Element_Select
{
    public $name = 'tone';

    public function __construct($options = null)
    {
        parent::__construct($this->name, $options);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('tone')))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->setMultiOptions(Data::$tones);
    }	
}