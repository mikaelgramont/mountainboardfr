<?php
class User_Form_Register extends Lib_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options, true);

        $action = Globals::getRouter()->assemble(array(), 'userregister', true);

        $this->setMethod('POST')
             ->setAction($action)
             ->setName('loginForm');

        $username = new Lib_Form_Element_Username(null, false, false, true);
        $username->setHint('pickYourUserNameHint');
        
        //$authMethod = new Lib_Form_Element_AuthMethod();

        $password = new Lib_Form_Element_Password(true);
        $passwordConfirm = new Lib_Form_Element_Password_Confirm(true, $this, $password->getName());

        //$identity = new Lib_Form_Element_OpenId(true, true);

        $email = new Lib_Form_Element_Email(true);

        $trickQuestion = new Lib_Form_Element_TrickQuestion('slide');
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(ucfirst(Globals::getTranslate()->_('registerButton')));

        $this->addElements(array(
            $username,
            //$authMethod,
            $password,
            $passwordConfirm,
            //$identity,
            $email,
            $trickQuestion,
            $submit
        ));
    }

    /**
     * Overload of isValid is necessary in order to set the token for
     * the password confirm 'IsIdentical' token and to choose between
     * login methods
     *
     * @param array $data
     * @return boolean
     */
    public function isValid($data)
    {
        $this->getElement(User::INPUT_PASSWORD)->setRequired(true);
        $this->getElement(User::INPUT_PASSWORD_CONFIRM)->setRequired(true);

        $valid = parent::isValid($data);
        return $valid;
    }
}