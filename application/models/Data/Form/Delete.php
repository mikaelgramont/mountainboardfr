<?php
class Data_Form_Delete extends Lib_Form 
{
    /**
     * Object to be delete via this form
     *
     * @var Data_Row
     */
    protected $_object;

    public function __construct(Data_Row $object, $options = null)
    {
		$this->_object = $object;
    	parent::__construct($options, true);
        $this->setAction($this->_object->getDeleteLink());
        $this->addElements(array($this->getSubmit()));
        $this->setAttrib('id', 'deleteForm');
    }
    
    /**
     * Factory for the submit element
     *
     * @return Zend_Form_Element_Submit
     */
    public function getSubmit($label = 'confirm')
    {
        $element = new Zend_Form_Element_Submit('delete');
        $element->setLabel(ucfirst(Globals::getTranslate()->_($label)));

        return $element;
    }    
}