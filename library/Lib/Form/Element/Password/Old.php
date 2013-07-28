<?php
/**
 * Confirmation of password
 */
class Lib_Form_Element_Password_Old extends Zend_Form_Element_Password
{
    public $name = User::INPUT_PASSWORD_OLD;

    public function __construct($required = false, $options = null)
    {
        parent::__construct($this->name, $options);
        $this->setLabel(ucfirst(Globals::getTranslate()->_('passwordOld')));
        if($required){
            $this->setRequired()
                 ->addValidator('NotEmpty', true);
        }

    }
}