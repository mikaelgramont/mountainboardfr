<?php
class Lib_Form_GoTo extends Lib_Form
{
	protected $_submitOptions;
	
	public function __construct($action, $selectOptions, $submitOptions = array(), $options = null, $csrfProtection = false)
	{
		if(!isset($options['class'])){
			$options['class'] = 'goToForm';
		} else {
			$options['class'] .= ' goToForm';	
		}
		 
		parent::__construct($options, $csrfProtection);
		$this->setAction($action);
		$this->addElement($this->_buildSelectElement($selectOptions));
		$this->addElement($this->_buildSubmitElement($submitOptions));
	}
	
    public function render(Zend_View_Interface $view = null)
    {
        $this->_setOwnDecorators();

        if (null !== $view) {
            $this->setView($view);
        }

        $content = '';
        foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }
        return $content;
    }

    protected function _buildSelectElement($selectOptions)
	{
        $element = new Zend_Form_Element_Select('goToParameter');
		$element->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             	->addFilter('HTMLPurifier')
             	->setMultiOptions($selectOptions);
             
		return $element;
	}
	
	protected function _buildSubmitElement($submitOptions)
	{
        $type = isset($submitOptions['type']) ? $submitOptions['type'] : null;
        if(!in_array($type, array('submit', 'img'))){
        	$type = 'submit';
        }
        
        if($type == 'submit'){
	        $label = isset($submitOptions['label']) ? $submitOptions['label'] : '';
			if(empty($label)){
				$label = 'goToButtonLabel';
			}
			$element = new Zend_Form_Element_Submit('goToButton', $label);
        } else {
 	        $src = isset($submitOptions['src']) ? $submitOptions['src'] : '';
			if(empty($src)){
				$src = IMAGES_PATH.DEFAULT_GOTO_IMAGE;
			}
			$element = new Zend_Form_Element_Image('goToButton');
			$element->setImage($src);       	
        }
		
		$element->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             	->addFilter('HTMLPurifier');
             
		return $element;
	}
	
	protected function _setOwnDecorators()
    {
        $this->clearDecorators();
        $this->addDecorator('FormElements')
             ->addDecorator('JsForm');
             
        $this->setElementDecorators(array(
            array('ViewHelper'),
        ));
    }	
}