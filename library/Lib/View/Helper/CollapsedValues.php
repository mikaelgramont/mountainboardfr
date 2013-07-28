<?php
class Lib_View_Helper_CollapsedValues extends Zend_View_Helper_Abstract
{
	public function collapsedValues($data, $constants)
	{
		$arrReturn = array();
		for($i = 1; $i <= count($constants); $i++){
			if(!isset($data[$i - 1])){
				break;
			}
			$char = $data[$i - 1];
			if($char == '1'){
				$arrReturn[] = $this->view->translate($constants[$i]);
			}
		}
		$return = implode(', ',$arrReturn);

		return $return;
	}
}