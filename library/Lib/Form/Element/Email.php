<?php
/**
 * Email form element with translation and validators
 */
class Lib_Form_Element_Email extends Zend_Form_Element_Text
{
    public function __construct($forbidExistingEmail = true, $required = true, $options = null)
    {
        $spec = User::INPUT_EMAIL;
        $ajaxValidatorClass = 'Lib_Form_Element_Email_Validate';

        if($forbidExistingEmail){
            if(!is_array($options)){
                $options = array();
            }
            $options['ajaxValidatorEvent'] = 'blur';
            $options['ajaxValidator'] = $ajaxValidatorClass;
        }


        parent::__construct($spec, $options);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('email')))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier');
        if($required){
            $this->setRequired()
                 ->addValidator('NotEmpty', true);
        }
        $this->addValidator(new Lib_Form_Element_Email_Validate($forbidExistingEmail),true);
    }
}