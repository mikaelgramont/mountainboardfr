<?php
class ExampleController extends Lib_Controller_Action
{
    public $ajaxable = array('format'=>array('html'));

    public function init()
    {
        /**
         * contextAction
         */
        parent::init();
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('contextlist', 'html')
                    ->initContext();


        /**
         * testBreadCrumbsAction
         */
        $breadCrumbs = Globals::getBreadCrumbs();
        $breadCrumbs->append('communityCat', 'Communauté');

    }

    public function indexAction()
    {
    }

    /**
     * Utilisation d'un helper d'action
     *
     */
    public function helperAction()
    {
        $this->view->result = $this->_helper->Examplehelper(5, 6);
    }

    /**
     * Juste une page pour piloter le contextlist
     *
     */
    public function contextAction()
    {

    }

    /**
     * Test de detection du contexte ajax
     * avec switch automatique de la vue
     * utilisee
     */
    public function contextlistAction()
    {
        // Pull a single comment to view.
        // When AjaxContext detected and request to /example/context/format/html
        // or /example/context/?format=html,
        // uses the example/context.ajax.phtml view script.
    }

    /**
     * CSS, keywords et titre personnalises
     *
     */
    public function calendarAction()
    {

    }

    /**
     * Zend_Pagination
     *
     */
    public function paginationAction()
    {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('example/paginationpartial.phtml');
        $userTable = new User();
        $select = $userTable->select();
        $page = Zend_Paginator::factory($select);
        $page->setPageRange(6);
        $page->setCurrentPageNumber($this->_getParam('page', 1));
        $page->setItemCountPerPage($this->_getParam('par', 1));
        $this->view->users = $page;
    }

    /**
     * Zend_Form
     */
    public function formAction()
    {
        $form = new Example_Form();
        if($this->_getParam('decorators', null)){
            $form->customize();
        }

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                echo 'success';
                exit;
            } else {
                $form->populate($formData);
            }
        }

        $this->view->form = $form;
    }

    /**
     * jQuery-enabled Zend_Form
     */
    public function jqueryformAction()
    {
        $form = new Example_Form();
        ZendX_JQuery::enableForm($form);

        // see http://www.whitewashing.de/blog/articles/92 as an exercice

        $date = new ZendX_JQuery_Form_Element_DatePicker(
            'dp1',
            array('jQueryParams' => array('defaultDate' => '2008/12/20'))
        );
        $date->setLabel('Date')
             ->addValidator('NotEmpty');

        $form->addElement($date);

        $autoComplete = new Lib_Form_Element_Person('rider');
        $autoComplete->setLabel('Rider');
        $form->addElement($autoComplete);

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                echo 'success';
                exit;
            } else {
                $form->populate($formData);
            }
        }

        $this->view->form = $form;
    }

    public function referencesAction()
    {
        $id = $this->_getParam('id',1);

        $spotTable = new Spot();
        $spot = $spotTable->find($id)->current();
        $this->view->spot = $spot;
    }

    public function dependencesAction()
    {
        $spots = $this->_user->findDependentRowset('Spot','Submitter');

        Zend_View_Helper_PaginationControl::setDefaultViewPartial('example/paginationpartial.phtml');
        $page = Zend_Paginator::factory($spots);
        $page->setPageRange(3)
             ->setCurrentPageNumber($this->_getParam('page', 1))
             ->setItemCountPerPage($this->_getParam('par', 5));
        $this->view->spots = $page;
    }

    public function testinsertsAction()
    {
        $spotsTable = new Spot();
        $spot = $spotsTable->createRow();

        $spot->title = "PontsJumeaux";
        $spot->date = date("Y-m-d H:i:s");
        $spot->submitter = $this->_user->{User::COLUMN_USERID};
        $spot->dpt = 31;
        $spot->status = 1;
        $spot->setTags(array('ponts','jumeaux','skatepark'));

        $locationTable = new Location();
        $location = $locationTable->createRow();
        $location->longitude = "2.0";
        $location->latitude = "3.0";
        $location->zoom = "13";
        $spot->setLocation($location);

        $spot->save();

        $this->view->spot = $spot;
    }

    public function testupdatesAction()
    {
        $id = $this->_getParam('id',18);
        $spotTable = new Spot();
        $spot = $spotTable->find($id)->current();

        $spot->title = "PontsJumeaux $id";
        $spot->setTags(array('ponts','jumeaux','skatepark', $id.$id));

        $location = $spot->getLocation();
        if(!$location){
            $locationTable = new Location();
            $location = $locationTable->createRow();
        }
        $location->longitude = 20.0;
        $location->latitude = 30.0;
        $location->zoom = "18";
        $spot->setLocation($location);

        $spot->save();

        $this->view->spot = $spot;
    }

    public function testgetitemsAction()
    {
        $date = "2008-10-10";
        $items = Item::getAllItemsPostedSince($date);
        $items2 = Item::getItemsPostedSince($date, 'event');

        $this->view->items = $items;
        $this->view->items2 = $items2;
    }

    public function testBreadCrumbsAction()
    {
        $breadCrumbs = Globals::getBreadCrumbs();
        $breadCrumbs->append('spots', 'Spots');
        $this->view->breadCrumbs = $breadCrumbs;
    }

    /**
     * Utilisation du plugin Init_Translate
     * Francais
     */
    public function translateFrAction()
    {
        Globals::getTranslate('fr');
    }

    /**
     * Utilisation du plugin Init_Translate
     * Anglais
     */
    public function translateEnAction()
    {
        Globals::getTranslate('en');
    }

    /**
     * Utilisation du plugin Init_Translate
     * Langue par defaut
     */
    public function translateAction()
    {
    }

    public function userexistsAction()
    {

    }
}