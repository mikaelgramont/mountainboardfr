<?php
class Lib_Form_Element_File extends Zend_Form_Element_File
{
	protected $_hint = null;

	/**
	 * Constructi
	 *
	 * @param string $name
	 * @param boolean $required
	 * @param string $destination
	 * @param int $maxSize
	 * @param array $options
	 */
	public function __construct($name, $required = true, $destination = null, $maxSize = null, $options = null, $allowedExtensions = null)
    {
        parent::__construct($name, $options);
        if(empty($maxSize)){
        	$maxSize = GLOBAL_UPLOAD_MAXSIZE;
        }

        if(empty($destination)){
        	$destination = GLOBAL_UPLOAD_DEST;
        }
        
        if(empty($allowedExtensions)){
        	$allowedExtensions = Media_Item_Photo::getAllowedExtensionsString();
        }
        
    	$this->getTransferAdapter()->addPrefixPath('Lib_Validate_File', 'Lib/Validate/File', 'validate');
    	$this->getTransferAdapter()->clearValidators();

	    $this->setDestination($destination)
	        	->setMaxFileSize($maxSize)
	        	->setValueDisabled(true)
	        	->clearValidators()
	        	->addValidator('Count', true, 1)
	        	->addValidator('Size', true, $maxSize)
	        	->addValidator('Extension', true, $allowedExtensions);
	
        if($required){
        	$this->setRequired()
                 ->addValidator('NotEmpty', true);
        }
	}
	
	public function setHint($hint)
	{
		$this->_hint = $hint;
	}
	
	public function getHint()
	{
		return $this->_hint;
	}
}