<?php
/**
 * Username form element with translation and validators
 */
class Lib_Form_Element_OpenId extends Zend_Form_Element_Text
{
    public $name = User::INPUT_OPENID_IDENTITY;

    public function __construct($required = false, $forbidExistingOpenId = false, $options = null)
    {
        $ajaxValidatorClass = 'Lib_Form_Element_OpenId_Validate';

        if($forbidExistingOpenId){
            if(!is_array($options)){
                $options = array();
            }
            $options['ajaxValidatorEvent'] = 'blur';
            $options['ajaxValidator'] = $ajaxValidatorClass;
        }

        parent::__construct($this->name, $options);

        $toLowerFilter = new Zend_Filter_StringToLower();
        $toLowerFilter->setEncoding(APP_PAGE_ENCODING);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('openIDIdentity')))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->addFilter('HtmlEntities')
             ->addFilter($toLowerFilter);
        if($required){
            $this->setRequired()
                 ->addValidator('NotEmpty', true);
        }

        if($forbidExistingOpenId){
            $this->addValidator(new $ajaxValidatorClass());
        }
    }
}