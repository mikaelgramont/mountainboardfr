<?php
class Lib_Form_Element_Textarea extends Zend_Form_Element_Textarea
{
	protected $_hint = null;
	
	public function setHint($hint)
	{
		$this->_hint = $hint;
		return $this;
	}
	
	public function getHint()
	{
		return $this->_hint;
	}
}