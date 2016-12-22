<?php
class ArticleController extends Lib_Controller_Action
{
    /**
     * Initialisations
     */
    public function init()
    {
        parent::init();
        Zend_Registry::set('Category', Category::ARTICLES);
    }

    public function indexAction()
    {
		$this->_helper->layout->setLayout('one-column');
		$this->view->wrapperIsCard = true;
    }
}