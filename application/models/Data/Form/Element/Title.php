<?php
class Data_Form_Element_Title extends Zend_Form_Element_Text 
{
	protected $_form;

	public function __construct($form, $options = null)
	{
		$this->_form = $form;
		parent::__construct('title', $options);

		$toLowerFilter = new Zend_Filter_StringToLower();
        $toLowerFilter->setEncoding(APP_PAGE_ENCODING);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('title')))
             ->setRequired(true)
             ->addValidator('NotEmpty')
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->addFilter($toLowerFilter);
	}
	
	public function getHint()
	{
		return 'titleHint';
	}
}