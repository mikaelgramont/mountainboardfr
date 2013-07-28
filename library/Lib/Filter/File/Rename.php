<?php
class Lib_Filter_File_Rename extends Zend_Filter_File_Rename
{
    public function filter($value)
    {
        $file   = $this->getNewName($value, true);
        if (is_string($file)) {
            return $file;
        }

    	$fileContents = file_get_contents($file['source']);
		if(false === $fileContents){
			return false;
		}

		$status = file_put_contents($file['target'], $fileContents);
		if(false === $status){
			throw new Lib_Exception_Media_Photo("Could not write file from '{$file['source']}' to '{$file['target']}'");
		}

        return $file['target'];
    }

    public function getNewName($value, $source = false)
    {
        $file = $this->_getFileName($value);
        if ($file['source'] == $file['target']) {
            return $value;
        }

        if (($file['overwrite'] == true) && (file_exists($file['target']))) {
            unlink($file['target']);
        }

        if (file_exists($file['target'])) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception(sprintf("File '%s' could not be renamed. It already exists.", $value));
        }

        if ($source) {
            return $file;
        }

        return $file['target'];
    }
}
