<?php
class Lib_View_Helper_Tags extends Zend_View_Helper_FormTextarea
{
	public function Tags($name, $value = null, array $params = array(), $attribs = array())
    {
		$js = <<<JS

$('#tags').tagbox({
	separator: /[,]/
});

JS;
		$this->view->JQuery()->addJavascriptFile($this->view->asset()->script('jquery.tagbox.js'));
		$this->view->jQuery()->addOnLoad($js);

    	if(is_array($value) && count($value) == 1 && empty($value[0])){
			$value = '';
		}

		$return = parent::formTextarea($name, $value, $params, $attribs);
		return $return;
    }
}