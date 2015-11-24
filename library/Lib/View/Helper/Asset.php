<?php
class Lib_View_Helper_Asset extends Zend_View_Helper_Abstract
{
	/**
	 * This needs to match the rules in Makefile to generate concatenated/minified files
	 * @var unknown_type
	 */
	public static $scriptGroups = array(
		'general.js' => array(
			SWFOBJECT_LOCAL_PATH,
			UPLOADIFY_PATH,
			'libForm.js',
			'libChat.js',
//			'libMaps.js',
			'autocomplete.js',
			'jquery.tablesorter.js',
			'jquery.tagbox.js',
			'libFacebookUpload.js',
			'yepnope.js',
			'jquery.cookie.js',
		),
	);


	public function asset()
	{
		return $this;
	}

	public function url($path)
	{
		$versionnedPath = $this->_getFile($path);
		return $versionnedPath;
	}

	public function script($path)
	{
		if(APPLICATION_ENV != 'development'){
			foreach(self::$scriptGroups as $groupFile => $files){
				foreach($files as $memberFile){
					if($path == $memberFile){
						$path = $groupFile;
						break;
					}
				}
			}
		}
		$return = $this->view->baseUrl().$this->_getFile('/'.SCRIPTS_PATH.$path);
		$return = $this->view->cdnHelper->jsUrl($return);
		return $return;
	}

	public function css($path)
	{
		$return = $this->view->baseUrl().$this->_getFile('/'.CSS_PATH.$path);
		$return = $this->view->cdnHelper->cssUrl($return);
		return $return;
	}

	public function image($path)
	{
		$return = $this->view->baseUrl().$this->_getFile('/'.IMAGES_PATH.$path);
		$return = $this->view->cdnHelper->url($return);
		return $return;
	}

	protected function _getFile($path)
	{
		if (APPLICATION_ENV != 'development') {
			return $path;
		}
		$lookupTable = Lib_AssetCache::getLookupTable();
		if(array_key_exists($path, $lookupTable)){
			$versionnedPath = $lookupTable[$path];
		} else {
			$versionnedPath = $path;
		}
		return $versionnedPath;
	}
}
