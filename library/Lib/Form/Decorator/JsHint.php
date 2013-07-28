<?php
class Lib_Form_Decorator_JsHint extends Zend_Form_Decorator_Abstract
{
    protected $_hintTag = 'span';
    protected $_hintClass = 'hint';
    protected $_hintIdSuffix = 'JsHint';
    
	public function render($content)
	{
        $view = $this->getElement()->getView();
        if (null === $view) {
            return $content;
        }
        
        $elements = $this->getElement()->getElements();
        if(empty($elements)){
        	return $content;
        }
        
        $focusElementIds = array();
        foreach($elements as $element){
	        if(method_exists($element, 'getFocusElementId')){
	        	$focusElementIds[] = '#'.$element->getFocusElementId();
	        } else {
	        	$focusElementIds[] = '#'.$element->getId();
	        }
        }		
        
        $selectorString =  implode(', ', $focusElementIds);
        
        $js = <<<JS
        $('$selectorString').focus(function(){
        	$(this).parent().addClass('active');
		}).blur(function(){
        	$(this).parent().removeClass('active');
		});
JS;
        $view->getHelper('jQuery')->addOnLoad($js);
        
        return $content;
	}
}