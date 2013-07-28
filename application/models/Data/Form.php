<?php
abstract class Data_Form extends Lib_Form implements Data_Form_DataInterface
{
    /**
     * Object to be submitted/edited via this form
     *
     * @var Data_Row
     */
    protected $_object;

    /**
     * Current user
     *
     * @var User_Row
     */
    protected $_user;

    /**
     * ACL object used to enforce restrictions
     * on fields
     *
     * @var Lib_Acl
     */
    protected $_acl;

    /**
     * Constructor
     *
     * @param Data_Row $data
     * @param User_Row $user
     * @param Lib_Acl $acl
     * @param array $options
     */
    public function __construct(Data_Row $object, User_Row $user, Lib_Acl $acl, $options = null)
    {
        parent::__construct($options);

        $this->_object = $object;
        $this->_user = $user;
        $this->_acl = $acl;

        $this->setAction($this->_object->getEditLink());
        $this->_setup();
    }

    /**
     * Returns a list of form elements that match the database columns
     *
     * @return array
     */
    protected function _setup()
    {
        $elements = array(
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'tags' => $this->getTags()
        );

        $isAllowedToEditAll = $this->_acl->isAllowed($this->_user, Lib_Acl::PUBLIC_EDIT_RESOURCE);
        $isAllowedToEditThis = $this->_acl->isAllowed(Lib_Acl::OWNER_ROLE.'_'.$this->_user->{User::COLUMN_USERID}, Lib_Acl::PUBLIC_EDIT_RESOURCE.'_'.$this->_user{User::COLUMN_USERID});
        $isAllowedToEdit = $isAllowedToEditThis || $isAllowedToEditAll;

        $isAdmin = $this->_acl->isAllowed($this->_user, Lib_Acl::ADMIN_RESOURCE);

        if(empty($this->_object->id)){
            // New post: we can decide to keep this hidden by specifying 'invalid' on submit
            $elements['status'] = $this->getStatus();
        } elseif($isAllowedToEdit){
            $elements['status'] = $this->getStatus();
        }

        if($isAdmin && !empty($this->_object->id)){
            $adminElements = array(
                'skipAutoFields' => $this->getSkipAutoFields(),
                'submitter' => $this->getSubmitter(),
                'date' => $this->getDate(),
                'lastEditionDate' => $this->getLastEditionDate(),
                'lastEditor' => $this->getLastEditor(),
            );
            $elements = array_merge($elements, $adminElements);
        }

        $this->addElements($elements);

        $this->addDisplayGroup(array('title', 'description'), 'documentGroup');
    	if($isAdmin && !empty($this->_object->id)){
            $this->addDisplayGroup(array('skipAutoFields', 'submitter', 'date', 'lastEditionDate',  'lastEditor'), 'autoFieldsGroup');
        }
        $this->addDisplayGroup(array('tags', 'status'), 'miscGroup');

        $this->addElements(array($this->getSubmit()));
    }

    protected function _setOwnDecorators()
    {
        parent::_setOwnDecorators();

        $reflection = new ReflectionClass($this->_object);
        if($reflection->implementsInterface('Data_Row_LocationInterface')){
            $this->addDecorator('JsMap');
        }
    }

    /**
     * Call special formatting functions before populating
     * form with data form database
     *
     * @param array $data
     */
    public function populateFromDatabaseData(array $data, $exceptionOnEmptyText = true)
    {
    	if($this->_object->id){
    		// Only get title and description from the database if they are there already
    		$data[Data_Form_Element::TITLE] = $this->_object->getTitle($exceptionOnEmptyText);
    		$data[Data_Form_Element::DESCRIPTION] = $this->_object->getDescription($exceptionOnEmptyText);
    	}
    	parent::populateFromDatabaseData($data);
    }

    public function getData()
    {
        return $this->_object;
    }

    /**
     * Factory for the title element
     *
     * @return Data_Form_Element_Title
     */
    public function getTitle()
    {
        $element = new Data_Form_Element_Title($this);
        return $element;
    }

    /**
     * Factory for the date element
     *
     * @return Lib_Form_Element_Date
     */
    public function getDate()
    {
        $locale = Zend_Registry::get('Zend_Locale');
        switch($locale){
            case 'fr':
                $format = 'dd/MM/YYYY H:m:s';
                break;
            default:
                $format = 'MM-dd-YYYY H:m:s';
                break;
        }
        $dateValidator = new Lib_Validate_DateTime($format, $locale);

        $element = new Lib_Form_Element_DateTime('date', true, array());
        $element->setLabel(ucfirst(Globals::getTranslate()->_('date')))
                ->addValidator($dateValidator);
        return $element;
    }

    /**
     * Factory for the submitter element
     *
     * @return Lib_Form_Element_Person
     */
    public function getSubmitter()
    {
        $element = new Lib_Form_Element_Username('submitter', true, true);
        $element->setLabel(ucfirst(Globals::getTranslate()->_('submitter')));
        return $element;
    }

    /**
     * Factory for the description element
     *
     * @return Zend_Form_Element_Textarea
     */
    public function getDescription()
    {
        $element = new Data_Form_Element_Description($this);
        return $element;
    }

    /**
     * Factory for the last edition date element
     *
     * @return Lib_Form_Element_Date
     */
    public function getLastEditionDate()
    {
         $locale = Zend_Registry::get('Zend_Locale');
        switch($locale){
            case 'fr':
                $format = 'dd/MM/YYYY H:m:s';
                break;
            default:
                $format = 'MM-dd-YYYY H:m:s';
                break;
        }
        $dateValidator = new Lib_Validate_DateTime($format, $locale);

        $element = new Lib_Form_Element_DateTime('lastEditionDate', true, array());
        $element->setLabel(ucfirst(Globals::getTranslate()->_('lastEditionDate')))
                ->addValidator($dateValidator);

       return $element;
    }

    /**
     * Factory for the last edition date element
     *
     * @return Lib_Form_Element_Date
     */
    public function getLastEditor()
    {
        $element = new Lib_Form_Element_Username('lastEditor', true, true);
        $element->setLabel(ucfirst(Globals::getTranslate()->_('lastEditor')))
                ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter');

        return $element;
    }

    /**
     * Factory for the submit element
     *
     * @return Zend_Form_Element_Submit
     */
    public function getSubmit($label = 'submit')
    {
        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel(ucfirst(Globals::getTranslate()->_($label)));

        return $element;
    }

    /**
     * Factory for the status element
     *
     * @return Lib_Form_Element_Status
     */
    public function getStatus()
    {
        $element = new Lib_Form_Element_Status('status');

        return $element;
    }

    /**
     * Factory for the tags element
     *
     * @return Zend_Form_Element_Textarea
     */
    public function getTags()
    {
        $element = new Lib_Form_Element_Tags('tags');
        $element->setLabel(ucfirst(Globals::getTranslate()->_('tags')))
                ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
                ->addFilter('HTMLPurifier');

        if(!empty($this->_object->id)){
            // Get tags
            $element->setValue(implode(',', $this->_object->getTags()));
        }

        return $element;
    }

    /**
     * Factory for skipAutoFields element
     *
     * @return Zend_Form_Element_Checkbox
     */
    public function getSkipAutoFields()
    {
        $element = new Lib_Form_Element_SkipAutoFields();
        return $element;
    }
}