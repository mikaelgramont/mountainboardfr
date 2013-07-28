<?php
class Lib_Form_Element_CKEditor extends Zend_Form_Element_Textarea
{
	public $helper = 'CKEditor';

	protected $_isAdvancedByDefault = false;

    public function isAdvanced()
    {
        if($this->_isAdvancedByDefault){
    		return true;
    	}

    	if(Globals::getUser()->status >= User::STATUS_EDITOR){
    		return true;
    	}

    	return false;
    }

	public function __construct($spec, $options = null)
	{
		if(!is_array($options)){
			$options = array();
		}

		if($this->isAdvanced()){
		    $options['advanced'] = 1;
		}

		parent::__construct($spec, $options);
	}
}