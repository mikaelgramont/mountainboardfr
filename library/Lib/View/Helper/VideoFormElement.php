<?php
class Lib_View_Helper_VideoFormElement extends Zend_View_Helper_FormElement
{
	public function videoFormElement($name, $value = null, $attribs = null)
	{
        $js = <<<JS
$("#{$name}").focus(
	function(){
  		if(this.value == this.defaultValue){
   			this.select();
  		}
 	}
)
JS;
        $this->view->jQuery()->addOnLoad($js);
        $attribs['class'] = 'mceNoEditor';
        return $this->view->formTextarea($name, $value, $attribs);		
	}
}