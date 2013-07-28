<?php
class Lib_Form_Element_Hash extends Zend_Form_Element_Hash
{
    /**
     * Initialize CSRF validator
     *
     * Creates Session namespace, and initializes CSRF token in session. 
     * Additionally, adds validator for validating CSRF token.
     * 
     * @return Zend_Form_Element_Hash
     */
    public function initCsrfValidator()
    {
        $session = $this->getSession();
        if (isset($session->hash)) {
            $rightHash = $session->hash;
        } else {
            $rightHash = null;
        }

        $this->addValidator(new Lib_Validate_CSRF($rightHash), true);
        return $this;
    }	
}