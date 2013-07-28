<?php
/**
 * Skip auto fields element
 */
class Lib_Form_Element_SkipAutoFields extends Zend_Form_Element_Checkbox
{
    public $name = 'skipAutoFields';

    public function __construct($options = null)
    {
        parent::__construct($this->name, $options);

        $this->setLabel(ucfirst(Globals::getTranslate()->_($this->name)))
             ->setCheckedValue('1')
             ->addFilter('Int');
    }

     public function render(Zend_View_Interface $view = null)
     {
         $this->addDecorator('SkipAutoFields');
         return parent::render($view);
     }
}