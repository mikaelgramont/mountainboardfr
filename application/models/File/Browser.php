<?php
class File_Browser
{
	protected $_root;
	protected $_currentPath;
	
	public function __construct($root, $currentPath = '')
	{
		$dir = realpath($root);
		if(!is_dir($dir)){
			throw new Lib_Exception("FileBrowser construction: path '$root' (translated to '$dir') is not a directory");
		}
		if(empty($currentPath)){
			$this->_currentPath = $this->_root = $dir;
		} else {
			$this->_currentPath = $root . DIRECTORY_SEPARATOR . $currentPath;
			$this->_root = $root;
		}
		
		$this->_currentPath = realpath($this->_currentPath);
		if(!is_dir($this->_currentPath)){
			throw new Lib_Exception("FileBrowser construction '$currentPath' is not a directory relative to root: '$root'");
		}		
	}
	
	public function getCurrentPath()
	{
		return $this->_currentPath;
	}
	
	public function moveTo($folderName)
	{
		$target = realpath($this->_currentPath . DIRECTORY_SEPARATOR . $folderName);
		if(!is_dir($target)){
			throw new Lib_Exception("FileBrowser descent into '$folderName': '$target' is not a directory");
		}
		
		if(strpos($target, $this->_root, 0) !== 0){
			throw new Lib_Exception("Unauthorized access attempt to '$target', with root directory set to '$this->_root'");
		}
		
		
		$this->_currentPath = $target;
	}
	
	/**
	 * Returns an array of directories and files in the current path
	 * @return array
	 */
	public function getFilesInCurrentPath($type)
	{
		$files = array();
		$dirs = array();
		$allowedExtensions = $this->_getAllowedExtensionsForBrowsingType($type);
		
		$dir = new DirectoryIterator($this->_currentPath);
		$up = $this->_currentPath . DIRECTORY_SEPARATOR . '..';
		$up = realpath($up);
		$upAllowed = strlen($this->_root) <= strlen($up);
		foreach($dir as $entry ){
			if(!$entry->isReadable()){
				continue;
			}
			
			$name = $entry->getFilename();
			
			if(!$entry->isDot() && !$entry->isDir()){
				if($this->_isVisible($name, $allowedExtensions)){
					$files[] =  array(
						'name' => $name,
						'size' => $entry->getSize(),
						'date' => $entry->getCTime(),
					);
				}
				continue;	
			}
			
			if($name == '..' && $upAllowed){
				$dirs[] = '..';
			} elseif(substr($name, 0, 1)  != '.') {
				$dirs[] = $name;
			}
		}
		
		$return = array(
			'folders' => $dirs,
			'files' => $files,
		);
		
		return $return;
	}
	
	public function stripAppRootFromCurrentPath()
	{
		$return = str_replace(APP_ROOT, '', $this->_currentPath);
		return $return;
	}
	
	protected function _getAllowedExtensionsForBrowsingType($type)
	{
		switch($type){
			case 'image':
				$return = explode(',', Media_Item_Photo::getAllowedExtensionsString());
				break;
			default:
				$return = Globals::getFileExtensionUploadWhiteList();
				break;
		}
		return $return;
	}
	
	protected function _isVisible($file, $allowedExtensions)
	{
		$infos = pathinfo($file);
		$extension = $infos['extension'];
		$visible = in_array($extension, $allowedExtensions);
		
		return $visible;
		
	}
}