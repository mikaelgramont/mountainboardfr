<?php
class Lib_Controller_Helper_DataPaginator extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Takes care of pagination
	 *
	 * @param Zend_Db_Select $select
	 * @param int $currentPage
	 * @param string $view
	 * @param int $itemsPerPage
	 * @param int $pageRange
	 * @return Zend_Paginator
	 */
	public function direct(Zend_Db_Select $select, $currentPage = 1, $view, $itemsPerPage = null, $pageRange = null)
	{
        if(empty($itemsPerPage)){
            $itemsPerPage = DEFAULT_ITEMS_PER_PAGE;
        }
        if(empty($pageRange)){
            $pageRange = DEFAULT_PAGERANGE;
        }

        $paginator = Zend_Paginator::factory($select);
        $paginator->setPageRange($pageRange); // Number of links displayed for browsing
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage($itemsPerPage);
        Zend_View_Helper_PaginationControl::setDefaultViewPartial($view);

        return $paginator;
    }		
}