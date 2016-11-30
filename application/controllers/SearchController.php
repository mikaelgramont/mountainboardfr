<?php
class SearchController extends Lib_Controller_Action
{
	public function indexAction()
	{
		Zend_Registry::set('Category', Category::NONE);

		$this->view->items = $this->view->results = $this->view->excerpts = array();
		$searchTerms = $this->view->searchTerms =
			$this->_request->getParam('searchterms');

		$search = new Search($this->_request->getParams());
		$search->setAdvancedForm();
		$form = $this->view->form = $search->getForm();

		if(empty($searchTerms)){
			// Display empty form
			return;
		}

		$form->setTerms($searchTerms);
		list($sortedItems, $excerpts, $searchInfo) = $search->execute($searchTerms, Globals::getGlobalCache());

		$this->view->searchInfo = $searchInfo;
		$this->view->items = $sortedItems;
		$this->view->excerpts = $excerpts;
	}
}