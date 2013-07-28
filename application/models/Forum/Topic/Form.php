<?php
class Forum_Topic_Form extends Data_Form
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
            'content' => $this->getContent()
        );

        $isAllowedToEditAll = $this->_acl->isAllowed($this->_user, Lib_Acl::PUBLIC_EDIT_RESOURCE);
        $isAdmin = $this->_acl->isAllowed($this->_user, Lib_Acl::ADMIN_RESOURCE);

        if($isAllowedToEditAll){
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

        $this->addDisplayGroup(array('title', 'content'), 'documentGroup');
        if($isAdmin){
            $this->addDisplayGroup(array('skipAutoFields', 'submitter', 'date', 'lastEditionDate',  'lastEditor', 'status'), 'autoFieldsGroup');
        } elseif($isAllowedToEditAll){
            $this->addDisplayGroup(array('status'), 'statusGroup');
        }

        $this->addElements(array($this->getSubmit()));
    }

    /**
     * Factory for the content element
     * Ugly: the content element is used as the data container for the comment
     *
     * @return Zend_Form_Element_Textarea
     */
    public function getContent()
    {
        $element = new Data_Form_Element_Content($this);
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
    		$data[Data_Form_Element::CONTENT] = $this->_object->getFirstPost()->getContent($exceptionOnEmptyText);
    	}

    	$formattedData = $data;
		$elements = $this->getElements();
        foreach($elements as $name => $element){
            if(method_exists($element, 'getValueFromDatabase')){
            	$rawValue = isset($data[$name]) ? $data[$name] : null;
                $formattedData[$name] = $element->getValueFromDatabase($rawValue);
            }
        }
        $this->populate($formattedData);
    }
}