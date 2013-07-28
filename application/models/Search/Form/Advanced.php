<?php
class Search_Form_Advanced extends Search_Form_Simple
{
	/**
	 * @var Search
	 */
	protected $_search;

	public function __construct(Search $search, $options = null, $csrfProtection = false)
	{
		if(empty($options)){
			$options = array();
		}
		$options['id'] = 'advancedSearch';

		parent::__construct($search, $options, $csrfProtection);

		/*
		foreach($this->_search->getAllowedTypes() as $type) {
        	$elements[] = $this->_getCheckBoxForType($type);
        }
        $this->addElements($elements);
        */

	}

    protected function _setOwnDecorators()
    {
        $this->clearDecorators();
        $this->addDecorator('FormElements')
        	 ->addDecorator('Form', array('class' => 'advancedSearch'));

        $this->setElementDecorators(array(
            array('ViewHelper'),
        ));
    }

	protected function _getCheckBoxForType($type)
	{
		$element = new Zend_Form_Element_Checkbox($type);
        $element->setLabel(ucfirst(Globals::getTranslate()->_($type)))
                ->setCheckedValue('1')
                ->addFilter('Int');

        return $element;
	}
}