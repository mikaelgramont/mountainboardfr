<?php
class Comment_Form extends Data_Form
{
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

	/**
     * Factory for the description element
     * Ugly: the description element is used as the data container for the comment
     *
     * @return Zend_Form_Element_Textarea
     */
    public function getContent()
    {
        $element = new Data_Form_Element_Content($this);
        $element->setLabel(ucfirst(Globals::getTranslate()->_('comment')));

        // No hint for comments
        $element->setHint(null);
        return $element;
    }

    public function getParentId()
    {
        $element = new Zend_Form_Element_Hidden('parentId');
        if($this->_object->parentItem){
            $element->setValue($this->_object->parentItem->id);
        }

        return $element;
    }

    public function getParentType()
    {
        $element = new Zend_Form_Element_Hidden('parentType');
        if($this->_object->parentItem){
            $element->setValue($this->_object->parentItem->getItemType());
        }

        return $element;
    }

    public function getTone()
    {
    	$return = new Lib_Form_Element_Tone();
    	return $return;
    }

	public function getDescription()
	{
		throw new Lib_Exception("Comment forms cannot be asked for their description");
	}

	public function getTitle()
	{
		throw new Lib_Exception("Comment forms cannot be asked for their title");
	}

    /**
     * Returns a list of form elements that match the database columns
     *
     * @return array
     */
    protected function _setup()
    {
        $parentId = $this->getParentId();
        if(empty($parentId)){
            $this->setAction($this->_object->getCreateLink());
        } else {
            $this->setAction($this->_object->getEditLink());
        }
        $elements = array(
        	'tone' => $this->getTone(),
            'content' => $this->getContent(),
            'parentId' => $this->getParentId(),
            'parentType' => $this->getParentType()
        );

        $isAdmin = $this->_acl->isAllowed($this->_user, Lib_Acl::ADMIN_RESOURCE);

        if($isAdmin && !empty($this->_object->id)){
        	$elements['status'] = $this->getStatus();
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

        if($isAdmin && !empty($this->_object->id)){
            $this->addDisplayGroup(array('skipAutoFields', 'status', 'submitter', 'date', 'lastEditionDate',  'lastEditor'), 'autoFieldsGroup');
        }

        $this->addElements(array($this->getSubmit()));
    }
}