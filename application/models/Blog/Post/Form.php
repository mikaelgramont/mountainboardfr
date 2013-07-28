<?php
class Blog_Post_Form extends Data_Form
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
            'tags' => $this->getTags(),
        );

        $isAllowedToEditAll = $this->_acl->isAllowed($this->_user, Lib_Acl::PUBLIC_EDIT_RESOURCE);
        $isAllowedToEditThis = $this->_acl->isAllowed(Lib_Acl::OWNER_ROLE.'_'.$this->_user->{User::COLUMN_USERID}, Lib_Acl::PUBLIC_EDIT_RESOURCE.'_'.$this->_user{User::COLUMN_USERID});
        $isAllowedToEdit = $isAllowedToEditThis || $isAllowedToEditAll;

        $isAdmin = $this->_acl->isAllowed($this->_user, Lib_Acl::ADMIN_RESOURCE);

        if(empty($this->_object->id)){
            // New post: we can decide to keep this article hidden by specifying 'invalid' on submit
            $elements['status'] = $this->getStatus();
        } elseif($isAllowedToEdit){
            $elements['status'] = $this->getStatus();
        }

        if($isAdmin){
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

        $this->addDisplayGroup(array('title', 'description', 'content',  'tags', 'status'), 'articleGroup');
        if($isAdmin){
            $this->addDisplayGroup(array('skipAutoFields', 'submitter', 'date', 'lastEditionDate',  'lastEditor'), 'autoFieldsGroup');
        }

        $this->addElements(array($this->getSubmit()));

    }

    /**
     * Factory for the content element
     *
     * @return Data_Form_Element_Content
     */
    public function getContent()
    {
        $element = new Data_Form_Element_Content($this);
        return $element;
    }
    
    /**
     * Factory for the content element
     *
     * @return Data_Form_Element_Description
     */
    public function getDescription()
    {
        $element = new Data_Form_Element_Description($this);
        return $element;
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
    		$data[Data_Form_Element::CONTENT] = $this->_object->getContent($exceptionOnEmptyText);
    	}
        
    	$formattedData = $data;
		$elements = $this->getElements();
        foreach($elements as $name => $element){
        	if(in_array($name, array('title', 'content'))){
        		continue;
        	}
            if(method_exists($element, 'getValueFromDatabase')){
            	$rawValue = isset($data[$name]) ? $data[$name] : null;
                $formattedData[$name] = $element->getValueFromDatabase($rawValue);
            }
        }
        $this->populate($formattedData);}
}