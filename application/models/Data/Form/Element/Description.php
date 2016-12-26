<?php
class Data_Form_Element_Description extends Lib_Form_Element_TinyMce
{
	protected $_form;

	protected $_isAdvancedByDefault = false;
	
	public function __construct($form, $options = null)
	{
		$this->_form = $form;
		
		parent::__construct('description', $options);
		$this->placeholder = ucfirst(
		    $this->getTranslator()->_($this->getHint()));
		
        $this->setLabel(ucfirst(Globals::getTranslate()->_('description')))
             ->setRequired(true)
             ->addValidator('NotEmpty')
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier');
	}

	public function getHint()
	{
		return 'descriptionHint';
	}
	
}