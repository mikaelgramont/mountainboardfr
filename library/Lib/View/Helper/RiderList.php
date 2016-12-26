<?php
class Lib_View_Helper_RiderList extends Zend_View_Helper_FormElement
{
    public function riderList($name, $value, $params = array(), $attribs = array())
    {
        $element = $this->view->form->getElement('riders');
		$list = $element->getList();

    	$js = <<<JS

$('#riders').tagbox({
	separator: /[,]/,
	autocomplete: "$list"
});

JS;

		$this->view->JQuery()->addJavascriptFile($this->view->asset()->script('jquery.tagbox.js'));
		$this->view->jQuery()->addOnLoad($js);

		if(is_array($value) && count($value) == 1 && empty($value[0])){
			$value = '';
		}
		$attribs['placeholder'] = $element->placeholder;
		$return = $this->view->formText($name, $value, $attribs);
    	return $return;
    }
}