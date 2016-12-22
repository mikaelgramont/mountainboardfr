<?php
class Search_Form_Simple extends Lib_Form
{
	/**
	 * @var Search
	 */
	protected $_search;

	public function __construct(
	    Search $search = null, $options = null, $csrfProtection = false,
	    $extraOptions = null)
	{
		if(empty($options)){
			$options = array();
		}
		$defaultOptions = array('id' => 'simpleSearch');
		$options = array_merge($defaultOptions, $options);

		parent::__construct($options, $csrfProtection);
		$this->_search = $search;

		$this->setMethod('GET');
		$this->setAction(Globals::getRouter()->assemble(array(), 'search'));

		$submitId =
		    (is_array($extraOptions) && isset($extraOptions['submitId'])) ?
		    $extraOptions['submitId'] : 'searchSubmit';
		$elements = array(
            'searchTerms' => $this->getSearchTerms(),
        	'submit' => $this->getSubmit(null, $submitId)
        );
        $this->addElements($elements);
	}

	public function setTerms($searchTerms)
	{
		$this->getElement('searchTerms')->setValue($searchTerms);
	}

	/**
     * Returns a list of form elements
     *
     * @return array
     */
    public function getSearchTerms()
    {
    	$searchTerms = new Zend_Form_Element_Text('searchTerms');
    	return $searchTerms;
    }

    public function getSubmit($label = 'searchSubmit', $submitId = null)
    {
        if (empty($label)) {
            $label = 'searchSubmit';
        }
        if (empty($submitId)) {
            $submitId = 'searchSubmit';
        }
        $element = new Zend_Form_Element_Submit(
            'searchSubmit', array(
                'label' => ucfirst(Globals::getTranslate()->_($label)),
                'value' => 'submit'));
        $element->id = $submitId;
        //$element->setValue(ucfirst(Globals::getTranslate()->_($label)));
		return $element;
    }

    protected function _setOwnDecorators()
    {
        $this->clearDecorators();
        $this->addDecorator('FormElements')
        	 ->addDecorator('Form', array('class' => 'simpleSearch'));

        $this->setElementDecorators(array(
            array('ViewHelper'),
        ));
    }

    protected function _setSubmitDecorators($element)
    {
        // Buttons do not need labels
        $element->setDecorators(array(
			array('ViewHelper'),
		));
    }
}