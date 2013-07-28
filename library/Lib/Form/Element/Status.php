<?php
/**
 * Status select element
 */
class Lib_Form_Element_Status extends Zend_Form_Element_Select
{
    public $name = 'status';

    public function __construct($options = null)
    {
        parent::__construct($this->name, $options);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('status')))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->setMultiOptions(array(
                    Data::VALID => Data::VALID,
                    Data::INVALID => Data::INVALID,
        ));
    }
}