<?php
class Lib_Form_Decorator_Hint extends Zend_Form_Decorator_Abstract
{
    protected $_hintTag = 'span';
    protected $_hintClass = 'hint';
    protected $_hintIdSuffix = 'Hint';
    
	public function render($content)
	{
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        if(!method_exists($element, 'getHint')){
        	return $content;
        }
        
        if(empty($this->_hintIdSuffix)){
        	return $content;
        }
        
        $hint = $element->getHint();
        if(empty($hint)){
        	return $content;
        }
        
        $elementId = $element->getId();
        $id = $elementId.$this->_hintIdSuffix;
        $message = ucfirst($view->translate($hint));
        $hint = '<'.$this->_hintTag.' class="'.$this->_hintClass.'" id="'.$id.'">'.$message.'</'.$this->_hintTag.'>';
        
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        
        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $hint;
            default:
            case self::PREPEND:
                return $hint . $separator . $content;
        }
	}
}