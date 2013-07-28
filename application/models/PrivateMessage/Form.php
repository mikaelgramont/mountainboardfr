<?php
class PrivateMessage_Form extends Data_Form
{
    /**
     * Returns a list of form elements that match the database columns
     *
     * @return array
     */
    protected function _setup()
    {
        $elements = array(
            'toUser' => $this->getToUser(),
            'content' => new Data_Form_Element_Content($this),
        );
        $this->addElements($elements);
        $this->addDisplayGroup(array('toUser','content'), 'mainGroup');
        $this->addElements(array($this->getSubmit()));
    }

    /**
     * Factory for 'to' element: a user that must exist
     *
     * @return Lib_Form_Element_Username
     */
    protected function getToUser()
    {
        $element = new Lib_Form_Element_Username('toUser', true, true);
        $element->setRequired(true);
        return $element;
    }
}