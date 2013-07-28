<?php
class User_Form_Login extends Lib_Form
{
    /**
     * Auth method chosen
     *
     * @var unknown_type
     */
    public $chosenAuthMethod = null;

    /**
     * Constructor
     *
     * @param array $options
     * @param string $action
     */
    public function __construct($options = null, $action = ".")
    {
        parent::__construct($options);

        $this->setMethod('POST')
             ->setAction($action)
             ->setName('loginForm');

        $username = new Lib_Form_Element_Username();
        $username->setHint('');
        $password = new Lib_Form_Element_Password();

        $remember = new Zend_Form_Element_Checkbox(User::INPUT_REMEMBER);
        $remember->setLabel(ucfirst(Globals::getTranslate()->_('rememberMe')))
                 ->setCheckedValue('1')
                 ->addFilter('Int');

        if(OPENID_ACTIVE){
            $authMethod = new Lib_Form_Element_AuthMethod();
            $identity = new Lib_Form_Element_OpenId();

            $errorData = Globals::getLoginErrorData();
            if(isset($errorData[User::LOGIN_METHOD_OPENID_REDIRECT]['messages'][0])){
                $login->addError($errorData[User::LOGIN_METHOD_OPENID_REDIRECT]['messages'][0]);
            }
            if(isset($errorData[User::LOGIN_METHOD_OPENID_POST]['messages'][0])){
                $login->addError($errorData[User::LOGIN_METHOD_OPENID_POST]['messages'][0]);
            }
        }

        $login = new Zend_Form_Element_Hidden(User::INPUT_LOGIN);
        $login->setValue(1)
              ->addFilter('Int')
              ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('loginFormSubmit');
        $submit->setLabel(ucfirst(Globals::getTranslate()->_('login')));


        if(OPENID_ACTIVE){
            $this->addElement($authMethod);
        }

        $this->addElements(array(
            $username,
            $password
        ));

        if(OPENID_ACTIVE){
            $this->addElement($identity);
        }

        $this->addElements(array(
            $remember,
            $login,
            $submit
        ));
    }

    /**
     * Just-in-time addition of validators, depending on auth method
     *
     * @param array $data
     * @return boolean
     */
    public function isValid($data)
    {
        if(!OPENID_ACTIVE){
            // Password is required
            $password = $this->getElement(User::INPUT_PASSWORD);
            $password->setRequired()->addValidator('NotEmpty',true);
            $status = parent::isValid($data);
            return $status;
        }

        /**
         * OpenId is active, let's find out the auth method chosen,
         * in order to apply the right validators
         */
        $this->chosenAuthMethod = isset($data[User::INPUT_AUTH_METHOD]) ? $data[User::INPUT_AUTH_METHOD] : User::LOGIN_AUTHMETHOD_PASSWORD;

        if($this->chosenAuthMethod == User::LOGIN_AUTHMETHOD_OPENID){
            $identity = $this->getElement(User::INPUT_OPENID_IDENTITY);
            $identity->setRequired()->addValidator('NotEmpty',true);
        } else {
            $password = $this->getElement(User::INPUT_PASSWORD);
            $password->setRequired()->addValidator('NotEmpty',true);
        }

        $status = parent::isValid($data);
        return $status;
    }
}