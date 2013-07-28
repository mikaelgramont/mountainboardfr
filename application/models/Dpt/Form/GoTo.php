<?php
class Dpt_Form_GoTo extends Lib_Form
{
    protected $_groupClass = 'element-group-simple';

    protected $_submitGroupClass = 'submit-group-simple';
    
    public function __construct($countryId = null, $options = null)
	{
        parent::__construct();
		$dpt = new Lib_Form_Element_Dpt('dpt', true, true);
		$dpt->setHint(null);
        if($countryId){
        	$dpt->country = $countryId;	
        }

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(ucfirst(Globals::getTranslate()->_('gotoDptSubmit')));

        $this->addElements(array($dpt, $submit));
	}
}