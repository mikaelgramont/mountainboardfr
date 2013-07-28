<?php 
class Lib_PaginatorFactory
{
	public static function getPaginator($data, $currentPage = 1, $itemsPerPage = null, $pageRange = null, $view = null)
	{
		if(empty($view)){
            $view = 'commonviews/pagination.phtml';
        }
        if(empty($itemsPerPage)){
            $itemsPerPage = DEFAULT_ITEMS_PER_PAGE;
        }
        if(empty($pageRange)){
            $pageRange = DEFAULT_PAGERANGE;
        }

        $paginator = Zend_Paginator::factory($data);
        $paginator->setPageRange($pageRange); // Number of links displayed for browsing
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage($itemsPerPage);
        Zend_View_Helper_PaginationControl::setDefaultViewPartial($view);

        return $paginator;
    }
}