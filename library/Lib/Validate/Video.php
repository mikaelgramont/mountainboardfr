<?php
class Lib_Validate_Video extends Zend_Validate_Abstract
{
    const NOT_VALID = 'videoCodeNotValid';

	/**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_VALID => "videoCodeNotValid",
    );

    public function isValid($value)
    {
    	$parser = new VideoInfoParser();
    	return $parser->isValid($value);
	}	
}