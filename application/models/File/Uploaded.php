<?php
class File_Uploaded extends File 
{
    /**
     * Moves an uploaded file to a specified destination,
     * returns the new File object
     *
     * @param string $fullDestination includes path and filename
     * @return File
     */
	public function moveTo($fullDestination)
    {
    	move_uploaded_file($this->getFullPath(), $fullDestination);
    	$newFile = new File($fullDestination);
    	return $newFile; 
    }
}