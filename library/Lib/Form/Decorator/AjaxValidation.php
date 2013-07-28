<?php
/**
 * Add a javascript event listener on each element onBlur event,
 * in order to perform ajax validation calls
 *
 */
class Lib_Form_Decorator_AjaxValidation extends Zend_Form_Decorator_Abstract
{
	protected $_validatorList = array(
	    0 => 'Lib_Form_Element_Username_Validate',
	    1 => 'Lib_Form_Element_Email_Validate',
	    2 => 'Lib_Form_Element_OpenId_Validate',
	    0 => 'Lib_Form_Element_Username_MustExist',
	);

	protected $_eventList = array(
	   'select',
	   'unload',
	   'keydown',
	   'keypress',
	   'mousedown',
	   'submit',
	   'click',
	   'scroll',
	   'dblclick',
	   'mouseout',
	   'mousemove',
	   'error',
	   'resize',
	   'blur',
	   'change',
	   'mouseup',
	   'keyup',
	   'load',
	   'focus',
	);

	public function render($content)
    {
        $element = $this->getElement();
        $elementName = $element->getName();
        $view = $element->getView();

        if(!isset($element->ajaxValidatorEvent) || !$element->ajaxValidatorEvent){
            return $content;
        }

        if(!in_array($element->ajaxValidatorEvent, $this->_eventList)){
            throw new Lib_Exception("Unknown ajax validation event: $element->ajaxValidatorEvent");
        }

        /**
         * Ugly: there as seen as attributes of the form element,
         * they must be removed in order to keep standards-compliant HTML
         */
        $validatorClass = $element->ajaxValidator;
        $event = $element->ajaxValidatorEvent;
        unset($element->ajaxValidatorEvent);
        unset($element->ajaxValidator);

        $validator = new $validatorClass();
        if(!method_exists($validator, 'getAjaxParams')){
            throw  new Lib_Exception("No getAjaxParams method in class $validatorClass");
        }
        $params = $this->_getJsParams($validator);
        $messages = $this->_getErrorMessages($validator);
        $arrValidatorId = array_flip($this->_validatorList);
        $id = 'id'.$arrValidatorId[$validatorClass];

        $js = <<<JS

$("#{$elementName}").{$event}(function(){
    Lib.Form.ajaxValidationHandler(this, '{$id}', {$messages}, {$params});
});
JS;
		$view->getHelper('jQuery')->addOnLoad($js);

        return $content;
    }


    protected function _getErrorMessages($validator)
    {
        $messages = $validator->getErrorTemplates();

        $translated = array();
		foreach($messages as $key => $message){
            $translated[] = addslashes(str_replace('-','',$key)).":". '"'.$validator->getTranslator()->_($key).'"';
        }

        $jsMessages = '{' . implode($translated, ", ") . '}';
        return $jsMessages;
    }

    protected function _getJsParams($validator)
    {
        $array = $validator->getAjaxParams();

        $params = array();
        foreach($array as $k=>$v){
            $value = addslashes($v);
            $params[] = "$k: '$value'";
        }

        return '{'.implode($params, ', ').'}';
    }
}
