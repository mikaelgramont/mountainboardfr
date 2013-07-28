<?php
class User_Form_Logout extends Lib_Form
{
    public function __construct($options = null, $action = ".")
    {
        parent::__construct($options);

        $this->setMethod('POST')
             ->setAction($action)
             ->setName('logoutForm');

        $logout = new Zend_Form_Element_Hidden(User::INPUT_LOGOUT);
        $logout->setValue(1)
               ->addFilter('Int')
               ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(ucfirst(Globals::getTranslate()->_('logout')));

        $this->addElements(array(
            $logout,
            $submit
        ));
    }
}