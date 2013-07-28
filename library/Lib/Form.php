<?php
/**
 * Custom form class that uses its own error rendering and display tags
 *
 */
class Lib_Form extends Zend_Form
{
    protected $_groupClass = 'element-group';

    protected $_submitGroupClass = 'submit-group';

    /**
     * Constructor
     *
     */
    public function __construct($options = null, $csrfProtection = false)
    {
        parent::__construct($options);
        $this->setName('form1');
        $this->addPrefixPath('Lib_Form_Decorator', 'Lib/Form/Decorator', 'Decorator');
        $this->addPrefixPath('Lib_Filter', 'Lib/Filter');
        $this->setTranslator(Globals::getTranslate());

        if($csrfProtection){
            $csrfProtection = new Lib_Form_Element_Hash('token');
            $this->addElement($csrfProtection);
        }
    }

    /**
     * Call special formatting functions before populating
     * form with data form database
     *
     * @param array $data
     */
    public function populateFromDatabaseData(array $data)
    {
        $formattedData = $data;
		$elements = $this->getElements();
        foreach($elements as $name => $element){
            if(method_exists($element, 'getValueFromDatabase')){
            	$rawValue = isset($data[$name]) ? $data[$name] : null;
                $formattedData[$name] = $element->getValueFromDatabase($rawValue);
            }
        }
        $this->populate($formattedData);
    }

    /**
     * Call special formatting function before storing data
     * into database
     *
     * @param array $data
     * @return array
     */
    public function getFormattedValuesForDatabase()
    {
        $formattedData = $this->getValues();

        foreach($this->getElements() as $name => $element){
            $value = $element->getValue();
            if(method_exists($element, 'getFormattedValueForDatabase')){
                $formattedData[$name] = $element->getFormattedValueForDatabase($value);
            }
        }
        return $formattedData;
    }

    /**
     * Render form
     * Clear default decorators for elements and groups, and redefine them
     * to include a custom error decorator, among other things
     *
     * @param Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        $this->_setOwnDecorators();

        $elements = $this->getElements();
        foreach($elements as $element){
            if($element instanceof Zend_Form_Element_File){
				// File elements require a 'File' decorator
            	$element->setDecorators(array(
            		array('AjaxValidation'),
            		array('File'),
					array('CustomErrors'),
					array('Hint'),
					array('Description'),
            		array('Label', array('separator'=>' ', 'class' => 'form-element-label')),
            		array('HtmlTag', array('tag' => 'p', 'class' => $this->_groupClass)),
        		));
            }

            if($element instanceof Zend_Form_Element_Hidden){
				// Zend_Form_Element_Hidden elements should not be rendered in a group at all
            	$element->setDecorators(array(
		            array('AjaxValidation'),
		            array('ViewHelper'),
		            array('CustomErrors'),
		            array('Hint'),
		            array('Description'),
		            array('Label', array('separator'=>' ', 'class' => 'form-element-label')),
		            array('HtmlTag', array('tag' => 'p', 'class'=>'element-group-hidden')),
        		));
            }

            if(($element instanceof Zend_Form_Element_Submit)){
            	$this->_setSubmitDecorators($element);
            }
        }

        $content = parent::render($view);
        return $content;
    }


	public function getAdditionalJs()
	{
		return '';
	}

    protected function _setSubmitDecorators($element)
    {
        // Buttons do not need labels
        $element->setDecorators(array(
			array('ViewHelper'),
			array('HtmlTag', array('tag' => 'p', 'class'=> $this->_submitGroupClass)),
		));
    }

    protected function _setOwnDecorators()
    {
        $this->clearDecorators();
        $this->addDecorator('FormElements')
             ->addDecorator('JsForm')
             ->addDecorator('JsValidation')
             ->addDecorator('JsHint');

        $this->setDisplayGroupDecorators(array(
            'FormElements',
            'Fieldset'
        ));

        $this->setElementDecorators(array(
            array('AjaxValidation'),
            array('ViewHelper'),
            array('CustomErrors'),
            array('Hint'),
            array('Description'),
            array('Label', array('separator'=>' ', 'class' => 'form-element-label')),
            array('HtmlTag', array('tag' => 'p', 'class'=>$this->_groupClass)),
        ));
    }
}