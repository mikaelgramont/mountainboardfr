<?php
class Trick_Form extends Data_Form
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
            'trickTip' => $this->getTrickTip(),
            'difficulty' => $this->getDifficulty(),
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

        $this->addDisplayGroup(array('title', 'description', 'trickTip', 'difficulty', 'tags', 'status'), 'trickGroup');
        if($isAdmin && !empty($this->_object->id)){
            $this->addDisplayGroup(array('skipAutoFields', 'submitter', 'date', 'lastEditionDate',  'lastEditor'), 'autoFieldsGroup');
        }

        $this->addElements(array($this->getSubmit()));
    }

    /**
     * Factory for difficulty element
     *
     * @return Lib_Form_Element_Difficulty
     */
    protected function getDifficulty()
    {
        $element = new Lib_Form_Element_Difficulty();
        return $element;
    }

    /**
     * Factory for trick tip element
     *
     * @return Zend_Form_Element_Textarea
     */
    protected function getTrickTip()
    {
        $element = new Lib_Form_Element_TinyMce('trickTip');
        $element->setLabel(ucfirst(Globals::getTranslate()->_('trickTip')))
                ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
                ->addFilter('HTMLPurifier');
        return $element;
    }
}