<?php
/**
 * Username form element with translation and validators
 */
class Lib_Form_Element_AuthMethod extends Zend_Form_Element_Select
{
    public $name = User::INPUT_AUTH_METHOD;

    public function __construct($options = null)
    {
        parent::__construct($this->name, $options);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('authMethod')))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->setMultiOptions(array(
                    User::LOGIN_AUTHMETHOD_PASSWORD => 'password',
                    User::LOGIN_AUTHMETHOD_OPENID  => 'openId'
        ));
    }
}