<?php
class Lib_Form_Element_Location_Flag extends Zend_Form_Element_Hidden
{
    public function __construct($required = true, $options = null)
    {
        parent::__construct('locationFlag', $options);
    
        if($required){
        	$this->addValidator(new Lib_Validate_LocationRequired());
        }
    }
}