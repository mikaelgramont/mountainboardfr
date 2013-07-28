<?php
class Lib_Validate_Album extends Lib_Validate_Data
{
    const ALBUMTYPENOTALLOWED = 'albumTypeNotAllowed';

	/**
     * @var array
     */
    protected $_messageTemplates = array(
        Lib_Validate_Data::DOES_NOT_EXIST => "albumDoesNotExist",
        Lib_Validate_Data::EXISTS => "albumExists",
        self::ALBUMTYPENOTALLOWED => "albumTypeNotAllowed",
    );
    
    /**
     * Defined by Zend_Validate_Interface
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $found = $this->_findData($value, true);
        
        switch($this->_constraint){
        	case Lib_Validate_Data::MUST_EXIST:
        		if(empty($found)){
		        	$this->_error(Lib_Validate_Data::DOES_NOT_EXIST);
        			return false;
        		}
        		break;
        	case Lib_Validate_Data::MUST_NOT_EXIST:
        		if(!empty($found)){
		        	$this->_error(Lib_Validate_Data::EXISTS);
        			return false;
        		}
        		break;
        	case self::ALBUMTYPENOTALLOWED:
        		if(empty($found)){
		        	$this->_error(Lib_Validate_Data::DOES_NOT_EXIST);
        			return false;
        		}
        		if($found->albumType != Media_Album::TYPE_SIMPLE){
            		$this->_error(self::ALBUMTYPENOTALLOWED);
	        		return false;
            	}
        		break;
        }
        
        return true;
    }    
}