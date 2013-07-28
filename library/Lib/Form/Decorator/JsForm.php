<?php
/**
 * Add a javascript event listener on each form submit,
 * and disables the form after successful validation
 * Add the javascript form validation file
 *
 */
class Lib_Form_Decorator_JsForm extends Zend_Form_Decorator_Form
{
    public function getOptions()
    {
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $view = $this->getElement()->getView();
        $view->JQuery()->addJavascriptFile($view->asset()->script('libForm.js'));

        $formName = $this->getElement()->getId();

        $elements = $this->getElement()->getElements();
        foreach($elements as $element){
            if($element instanceof Zend_Form_Element_Submit){
                $submitName = $element->getId();
            }
        }

        $additionalJs = $this->getElement()->getAdditionalJs();

        if(!empty($submitName)){
            $js = <<<JS
$additionalJs

$("#{$formName}").submit(function(){
    var status = Lib.Form.submitHandler(this);
    if(status){
        $("#{$submitName}").get(0).disabled = "disabled";
    }
    return status;
});
JS;
            $this->getElement()->getView()->JQuery()->addOnLoad($js);
        };

        return parent::getOptions();
    }
}
