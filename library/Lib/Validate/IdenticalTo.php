<?php
/**
 * Validator that makes sure that two fields hold the same value
 */
class Lib_Validate_IdenticalTo extends Zend_Validate_Abstract
{
    const NOT_SAME      = 'notSame';
    const MISSING_REFERENCE = 'missingReference';

    protected $_messageTemplates = array(
        self::NOT_SAME      => 'Fields do not match',
        self::MISSING_REFERENCE => 'No reference was provided to match against',
    );

    /**
     * Form holding both fields
     *
     * @var Zend_Form
     */
    protected $_form;

    /**
     * Name of the form element that will serve as a reference
     *
     * @var string
     */
    protected $_reference;

    public function setForm(Zend_Form $form)
    {
        $this->_form = $form;
    }

    public function setReference($reference)
    {
        $this->_reference = $reference;
    }

    public function getReference()
    {
        return $this->_reference;
    }

    public function __construct(Zend_Form $form, $reference = null)
    {
        $this->setForm($form);

        if (null !== $reference) {
            $this->setReference($reference);
        }
    }

    public function isValid($value)
    {
        $args = func_get_args();

        // In order to circumvent the limitation in argument numbers imposed
        // by Zend_Validate_Interface, we have to sneak a 2nd argument:
        $context = isset($args[1]) ? $args[1] : array();

        $this->_setValue($value);

        if (empty($this->_reference)) {
            $this->_error(self::MISSING_REFERENCE);
            return false;
        }

        $referenceElement = $this->_form->getElement($this->_reference);
        if(empty($referenceElement)){
            throw new Lib_Exception("Reference element {$this->_reference} does not exist in form.");
        }

        if(!isset($context[$this->_reference])){
            throw new Lib_Exception("No value for {$this->_reference} in context.");
        }

        $referenceValue = $context[$this->_reference];
        if ($value !== $referenceValue)  {
            $this->_error(self::NOT_SAME);
            return false;
        }

        return true;
    }
}