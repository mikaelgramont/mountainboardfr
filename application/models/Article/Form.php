<?php
class Article_Form extends Document_Form implements Data_Form_ArticleInterface,
													Data_Form_DocumentInterface
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
            'author' => $this->getAuthor(),
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

        $this->addDisplayGroup(array('title', 'description', 'author', 'content',  'tags', 'status'), 'articleGroup');
        if($isAdmin){
            $this->addDisplayGroup(array('skipAutoFields', 'submitter', 'date', 'lastEditionDate',  'lastEditor'), 'autoFieldsGroup');
        }

        $this->addElements(array($this->getSubmit()));

    }

    protected function _setOwnDecorators()
    {
        parent::_setOwnDecorators();
		if($this->_object->id){
			$options = array(
				'uploadFolder' => $this->_object->getFolderPath() . '/'
			);
		} else {
			$options = array();
		}
        
        $this->addDecorator('Uploadify', $options);
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
     * Call special formatting functions before populating
     * form with data form database
     *
     * @param array $data
     */
    public function populateFromDatabaseData(array $data, $exceptionsOnEmptyText = true)
    {
    	if($this->_object->id){
    		// Only get content from the database if it is there already 
    		$data[Data_Form_Element::CONTENT] = $this->_object->getContent();
    	}
    	parent::populateFromDatabaseData($data);
    }
}