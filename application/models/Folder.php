<?php
class Folder
{
	protected $_path;

	public function __construct($path)
	{
		$this->_path = realpath($path);
	}

	public function getPath()
	{
		return $this->_path;
	}

	public function isWritableBy(User_Row $user)
	{
		if($user->status == User::STATUS_ADMIN){
			return true;
		}

		if($user->status == User::STATUS_EDITOR && strpos($this->_path, CONTENT_DIRECTORY)){
			return true;
		}

		if($user->status == User::STATUS_WRITER && strpos($this->_path, CONTENT_DIRECTORY)){
			return true;
		}

		if($user->getUploadFolder()->getPath() === $this->_path){
			return true;
		}

		return false;
	}

	/**
	 * Create a folder
	 *
	 * @param string $path
	 * @return Folder
	 */
	public static function create($path)
	{
		$status = mkdir($path);
		if(!$status){
			throw new Lib_Exception("Folder creation attempt failed. Path: '$path'");
		}
		$folder = new self($path);
		return $folder;
	}

	/**
	 * Delete an empty folder
	 * @return boolean
	 */
	public function delete()
	{
		$status = rmdir($this->_path);
		return $status;
	}

	/**
	 * Recursively empty this folder
	 *
	 * @param string|null $dir the path relative to this folder
	 * @param boolean $deleteThisToo whether to delete this folder or not
	 */
	public function emptyContent($dir = null, $deleteThisToo = false)
	{
	    if($dir === null){
	    	$dir = $this->getPath();
	    	$isRoot = true;
	    } else {
	    	$isRoot = false;
	    }
		if(!$dh = @opendir($dir)){
	    	return;
	    }
	    while (false !== ($obj = readdir($dh))) {
	        if($obj=='.' || $obj=='..'){
	        	continue;
	        }

	        if($obj == '.svn' && $isRoot && !$deleteThisToo){
	        	// Do not delete .svn directory in root directory if we wish to keep the root directory
	        	continue;
	        }
	        if (!@unlink($dir.'/'.$obj)){
	        	$this->emptyContent($dir.'/'.$obj, true);
	        }
	    }

	    closedir($dh);
	    if ($deleteThisToo){
	        @rmdir($dir);
	    }
	}

	public function copyContentTo($destination)
	{
		if(!$dh = @opendir($this->_path)){
	    	throw new Lib_Exception("Folder '$this->_path' does not exist");
	    }

	    while(false !== ($obj = readdir($dh))) {
	        if($obj=='.' || $obj=='..' || $obj == '.svn'){
	        	continue;
	        }

	        if(is_dir($this->_path . DIRECTORY_SEPARATOR . $obj)){
	        	throw new Lib_Exception("Recursive copy of folders is not supported: " .$this->_path . DIRECTORY_SEPARATOR . $obj);
	        }

	        $status = copy($this->_path . DIRECTORY_SEPARATOR . $obj, $destination . DIRECTORY_SEPARATOR . $obj);
	    }
	}
}
