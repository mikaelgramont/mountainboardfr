<?php
class User_Form_OpenIdLogin extends Lib_Form
{
    public function __construct($options = null, $action = ".")
    {
        parent::__construct($options);

        $this->setMethod('POST')
             ->setAction($action)
             ->setName('openIdLoginForm');

        $identity = new Lib_Form_Element_OpenId(true);

        $login = new Zend_Form_Element_Hidden(User::INPUT_OPENID_LOGIN);
        $login->setValue(1)
              ->addFilter('Int')
              ->setRequired(true);

        $errorData = Globals::getLoginErrorData();
        if(isset($errorData[User::LOGIN_METHOD_OPENID_REDIRECT]['messages'][0])){
            $login->addError($errorData[User::LOGIN_METHOD_OPENID_REDIRECT]['messages'][0]);
        }
        if(isset($errorData[User::LOGIN_METHOD_OPENID_POST]['messages'][0])){
            $login->addError($errorData[User::LOGIN_METHOD_OPENID_POST]['messages'][0]);
        }

        $submit = new Zend_Form_Element_Submit('openIdLoginFormSubmit');
        $submit->setLabel(ucfirst(Globals::getTranslate()->_('login')));

        $this->addElements(array(
            $identity,
            $login,
            $submit
        ));
    }
}