<?php
class Lib_Validate_CSRF extends Zend_Validate_Identical
{
    /**
     * Error codes
     * @const string
     */
    const NOT_SAME_2      = 'notSame2';
    const MISSING_TOKEN_2 = 'missingToken2';

    protected $_messageTemplates = array(
        self::NOT_SAME_2      => "notSame2",
        self::MISSING_TOKEN_2 => 'missingToken2',
    );

    
	/**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if a token has been set and the provided value
     * matches that token.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue((string) $value);
        $token        = $this->getToken();

        if ($token === null) {
            $this->_error(self::MISSING_TOKEN_2);
            return false;
        }

        if ($value !== $token)  {
            $this->_error(self::NOT_SAME_2);
            return false;
        }

        return true;
    }	
}