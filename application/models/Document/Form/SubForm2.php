<?php
abstract class Document_Form_SubForm2 extends Data_Form implements Data_Form_DocumentInterface
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
            'tags' => $this->getTags()
        );

        $isAdmin = $this->_acl->isAllowed($this->_user, Lib_Acl::ADMIN_RESOURCE);

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

        $this->addDisplayGroup(array('title', 'description', 'author',  'tags'), 'documentGroup');
        if($isAdmin){
            $this->addDisplayGroup(array('skipAutoFields', 'submitter', 'date', 'lastEditionDate',  'lastEditor'), 'autoFieldsGroup');
        }

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