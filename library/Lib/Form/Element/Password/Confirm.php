<?php
/**
 * Confirmation of password
 */
class Lib_Form_Element_Password_Confirm extends Zend_Form_Element_Password
{
    public $name = User::INPUT_PASSWORD_CONFIRM;

    public function __construct($required = false, $form = null, $reference = "", $options = null)
    {
        parent::__construct($this->name, $options);
        $this->setLabel(ucfirst(Globals::getTranslate()->_('passwordConfirm')));

        $identicalTo = new Lib_Validate_IdenticalTo($form, $reference);
        $this->addValidator($identicalTo, true);

        if($required){
            $this->setRequired()
                 ->addValidator('NotEmpty', true)
                 ->addValidator('StringLength', true, array(6,null));
        }
    }
}