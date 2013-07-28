<?php
class Document_Form_SubForm1 extends Data_Form
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
        );

        $this->addElements($elements);

        $this->addDisplayGroup(array('title'), 'documentGroup');

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
        $element->setLabel(ucfirst(Globals::getTranslate()->_('author')))
                ->addValidator('NotEmpty')
                ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
        		->addFilter('HTMLPurifier');
        return $element;
    }	
}