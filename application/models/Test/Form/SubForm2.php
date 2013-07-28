<?php
class Test_Form_SubForm2 extends Article_Form_SubForm2
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
            'category' => $this->getTestCategory(),
            'author' => $this->getAuthor(),
            'content' => $this->getContent(),
            'tags' => $this->getTags(),
        );

        $this->addElements($elements);

        $this->addDisplayGroup(array('title', 'description', 'category', 'author', 'content',  'tags', 'status'), 'articleGroup');
        $this->addElements(array($this->getSubmit()));
	}
	
	public function getTestCategory()
	{
		$element = new Lib_Form_Element_Test_Category();
		return $element;
	}
}