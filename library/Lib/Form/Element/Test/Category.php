<?php
class Lib_Form_Element_Test_Category extends Zend_Form_Element_Select
{
    public $name = 'category';

    public function __construct($options = null)
    {
        parent::__construct($this->name, $options);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('category')))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->setMultiOptions(Test_Category::$available);
    }
}