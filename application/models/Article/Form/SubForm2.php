<?php
class Article_Form_SubForm2 extends Document_Form implements Data_Form_ArticleInterface
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

        $this->addElements($elements);
        $this->addDisplayGroup(array('title', 'description', 'author', 'content', 'tags'), 'articleGroup');
        $this->addElements(array($this->getSubmit()));

    }

    protected function _setOwnDecorators()
    {
        parent::_setOwnDecorators();
        /**
         * @todo: decider qui peut avoir uploadify
         */
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
     * @return Lib_Form_Element_Person
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