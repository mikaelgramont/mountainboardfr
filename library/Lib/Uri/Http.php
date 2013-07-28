<?php
class Lib_Uri_Http extends Zend_Uri_Http
{
    public static function fromString($uri)
    {
        if (is_string($uri) === false) {
            require_once 'Zend/Uri/Exception.php';
            throw new Zend_Uri_Exception('$uri is not a string');
        }

        $uri            = explode(':', $uri, 2);
        $scheme         = strtolower($uri[0]);
        $schemeSpecific = isset($uri[1]) === true ? $uri[1] : '';

        if (in_array($scheme, array('http', 'https')) === false) {
            require_once 'Zend/Uri/Exception.php';
            throw new Zend_Uri_Exception("Invalid scheme: '$scheme'");
        }

        $schemeHandler = new Lib_Uri_Http($scheme, $schemeSpecific);
        return $schemeHandler;
    }

	public function valid()
	{
		return true;
	}
}