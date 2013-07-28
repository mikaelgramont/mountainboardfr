<?php
class Lib_Form_Element_TrickQuestion extends Zend_Form_Element_Text 
{
	protected $_answer;

	public function __construct($answer = null, $options = null)
	{
		$this->_answer = $answer;
		parent::__construct('trickQuestion', $options);

		$toLowerFilter = new Zend_Filter_StringToLower();
        $toLowerFilter->setEncoding(APP_PAGE_ENCODING);

        $validator = new Lib_Validate_TrickQuestion($answer);
        
        $this->setLabel(ucfirst(Globals::getTranslate()->_('trickQuestion')))
             ->setRequired(true)
             ->addValidator('NotEmpty')
             ->addValidator($validator)
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->addFilter($toLowerFilter);
	}
	
	public function getHint()
	{
		return 'trickQuestionHint';
	}
}