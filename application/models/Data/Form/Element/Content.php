<?php
class Data_Form_Element_Content extends Lib_Form_Element_TinyMce
{
	protected $_form;

	protected $_isAdvancedByDefault = true;

	protected $_hint;
	
	public function __construct($form, $options = null)
	{
		$this->_form = $form;
		parent::__construct('content', $options);
        $this->setLabel(ucfirst(Globals::getTranslate()->_('content')))
             ->setRequired(true)
             ->addValidator('NotEmpty')
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             //->addFilter('AutoLink');
             ->addFilter('HTMLPurifier');
             
        $this->_hint = 'contentHint';
	}

	public function getHint()
	{
		return $this->_hint;
	}
	
	public function setHint($hint)
	{
		$this->_hint = $hint;
	}
}