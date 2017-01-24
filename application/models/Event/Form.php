<?php
class Event_Form extends Article_Form
{
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
        	'content' => $this->getContent(),
        	'author' => $this->getAuthor(),
        	'startDate' => $this->getStartDate(),
        	'endDate' => $this->getEndDate(),
        	'type' => $this->getType(),
        	'compContent' => $this->getCompContent(),
        	'compLevel' => $this->getCompLevel(),
        	'spot' => $this->getSpot(),
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
        $this->addDisplayGroup(array('author', 'startDate', 'endDate', 'spot', 'type', 'compContent', 'compLevel', 'content'), 'eventGroup');
        if($isAdmin && !empty($this->_object->id)){
            $this->addDisplayGroup(array('skipAutoFields', 'submitter', 'date', 'lastEditionDate',  'lastEditor'), 'autoFieldsGroup');
        }   
        $this->addDisplayGroup(array('tags', 'status'), 'miscGroup');     
        
        $this->addElements(array($this->getSubmit()));
    }
    
    /**
     * Factory for the author element
     *
     * @return Lib_Form_Element_Person
     */
    public function getAuthor()
    {
        $element = new Lib_Form_Element_Username('author', true);
        $element->setLabel(ucfirst(Globals::getTranslate()->_('organizer')))
                ->addValidator('NotEmpty');
        return $element;
    }    

    public function getStartDate()
    {
    	return $this->_geEventDate('startDate');
    }
    
    public function getEndDate()
    {
    	return $this->_geEventDate('endDate');
    }
    
    protected function _geEventDate($spec)
    {
        $locale = Zend_Registry::get('Zend_Locale');
        switch($locale){
            case 'fr':
                $format = 'dd/mm/YYYY';
                break;
            default:
                $format = 'mm-dd-YYYY';
                break;
        }

        $dateValidator = new Zend_Validate_Date($format, $locale);
        $date = new Lib_Form_Element_Date($spec,array());
        $options = array('yearRange' => (string)(date('Y') - 1) . ':'. (string)(date('Y') + 2));
        $date->setLabel(ucfirst(Globals::getTranslate()->_($spec)))
             ->setOptions($options)
             ->addValidator($dateValidator);
		return $date;
    }

    /**
     * Factory for spot element
     *
     * @return Lib_Form_Element_Spot
     */
    public function getSpot()
    {
        $element = new Lib_Form_Element_Spot('spot',true);
        $element->setLabel(ucfirst(Globals::getTranslate()->_('spot')));
        return $element;
    }

    public function getType()
    {
        $element = new Lib_Form_Element_Event_Type();
        return $element;
    }
    
    
	public function getCompContent()
	{
		$element = new Lib_Form_Element_Event_CompContent();
		return $element;
	}

	public function getCompLevel()
	{
		$element = new Lib_Form_Element_Event_CompLevel();
		return $element;
	}
}