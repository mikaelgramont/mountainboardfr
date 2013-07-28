<?php
class Lib_View_Helper_DataPaginator extends Zend_View_Helper_Abstract
{
	const TYPE_ONE_DIMENSION = 1;
	const TYPE_TWO_DIMENSIONS = 2;
	
	public function dataPaginator($type = null, Zend_Paginator $paginator = null, $scrollingStyle = null, $partial = null, $params = null)
	{
		if($type == self::TYPE_ONE_DIMENSION){
			$content = $this->view->paginationControl($paginator, $scrollingStyle, $partial, $params);
		} else {
			$content = "<div>two-dimension pagination to be implemented</div>";
		}
		return $content;
	}
}