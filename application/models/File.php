<?php
class File
{
    /**
     * Full path of the directory containing this file
     *
     * @var string
     */
	protected $_currentPath;
    /**
     * Name of this file
     *
     * @var string
     */
	protected $_currentName;
    /**
     * Name of this file, not including the extension
     *
     * @var string
     */
	protected $_baseFilename;
    /**
     * Full path of this file (directory + name + extension)
     *
     * @var string
     */
	protected $_fullPath;
    /**
     * This file's extension
     *
     * @var string
     */
	protected $_extension;
    /**
     * This file's size in bytes
     *
     * @var string
     */
	protected $_size;

    /**
     * Sets up information for file
     *
     * @param string $fullPath
     * @return void
     */
    public function __construct($fullPath)
    {
        $this->setFullPath($fullPath);
    }

    public function getFullPath() {return $this->_fullPath;}
    public function getPath() {return $this->_currentPath;}
    public function getName() {return $this->_currentName;}
    public function getBaseFilename() {return $this->_baseFilename;}
    public function getExtension() {return $this->_extension;}
    public function getFileSize() {return $this->_size;}

    /**
     * Sets the full path of the directory containing this file
     *
     * @param string $path
     */
    protected function _setPath($path)
    {
        $this->_currentPath = $path;
        $this->_updateInformation();
    }

    /**
     * Sets the full name of this file WITHOUT RENAMING IT
     *
     * @param string $name
     */
    protected function _setName($name)
    {
        $this->_currentName = $name;
        $this->updateInformation();
    }

    /**
     * Performs a refresh of all attributes
     *
     */
    protected function _updateInformation()
    {
        $this->setFullPath($this->_currentPath . DIRECTORY_SEPARATOR . $this->_currentName);
    }

    /**
     * Determines and stores all attributes of this file
     *
     * @param string $fullPath
     */
    public function setFullPath($fullPath)
    {
        if(!is_file($fullPath)){
        	throw new Lib_Exception("File does not exist: '$fullPath'");
        }

        $this->_fullPath = $fullPath;
        $pathInfos = pathinfo($fullPath);
        $this->_currentPath  = $pathInfos['dirname'];
        $this->_currentName  = $pathInfos['basename'];
        $this->_baseFilename = $pathInfos['filename'];
        $this->_extension    = isset($pathInfos['extension']) ? $pathInfos['extension'] : '';
        $this->_size = filesize($this->_fullPath);
    }

    /**
     * Performs copy of a file
     *
     * @param string $destination
     * @return File
     */
    public function copy($destination)
    {
        $copy = clone $this;
        $source = $copy->getFullPath();

        $status = copy($source, $destination);

        if(!$status) {
        	throw new Lib_Exception("An error occured while copying file '$source' to: '$destination'");
        }
        $copy->setFullPath($destination);

        $this->_updateInformation();
        return $copy;
    }

    /**
     * Moves object
     *
     * @param string $path Must not include trailing slash
     * @return boolean
     */
    public function move($path)
    {
        $path .= DIRECTORY_SEPARATOR.$this->_currentName;
        $status = rename($this->_fullPath, $path);
        if(!$status){
        	return false;
        }
        $this->setFullPath($path);
        return true;
    }

    /**
     * Renames object
     *
     * @param string $path
     * @return boolean
     */
    public function rename($name, $overwrite=false)
    {
        $newPath = $this->_currentPath.DIRECTORY_SEPARATOR.$name;
        if(is_file(($newPath)) && !$overwrite){
        	return false;
        }

        $status = @rename($this->_fullPath, $newPath);
        if(!$status){
        	return false;
        }
        $this->setFullPath($newPath);
        return true;
    }

    /**
     * Deletes file associated to this object
     * @return boolean
     */
    public function delete()
    {
        $status = unlink($this->_fullPath);
        return $status;
    }
}