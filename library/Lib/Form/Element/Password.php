<?php
/**
 * Password form element with translation and validators
 */
class Lib_Form_Element_Password extends Zend_Form_Element_Password
{
    public $name = User::INPUT_PASSWORD;

    public function __construct($required = false, $options = null)
    {
        parent::__construct($this->name, $options);
        $this->setLabel(ucfirst(Globals::getTranslate()->_('password')));
        if($required){
            $this->setRequired()
                 ->addValidator('NotEmpty',true);
        }
    }
}