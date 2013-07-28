<?php
class User_Form_LostPassword extends Lib_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $action = Globals::getRouter()->assemble(array(), 'lostpassword', true);

        $this->setMethod('POST')
             ->setAction($action)
             ->setName('lostPasswordForm');

        $username = new Lib_Form_Element_Username();

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(ucfirst(Globals::getTranslate()->_('sendPasswordButton')));

        $this->addElements(array(
            $username,
            $submit
        ));
    }
}