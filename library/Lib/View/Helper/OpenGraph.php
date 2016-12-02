<?php
class Lib_View_Helper_OpenGraph extends Zend_View_Helper_Abstract
{
	const DESCRIPTION = "og:description";
	const IMAGE = "og:image";
	const TITLE = "og:title";
	const URL = "og:url";
	
	/*
<meta property="og:title" content="First test" />
<meta property="og:description" content="Default kicker. - Design your next kicker in 3D!" />
<meta property="og:url" content="http://drawmeakicker.com/?id=1" />
<meta property="og:image" content="http://drawmeakicker.com/images/default-kicker.png" />
	 */
	protected $_metas = array();
	
	public function openGraph()
	{
		return $this;
	}
	
	public function forMedia($media)
	{
		$this->_metas[self::TITLE] = strip_tags($media->getTitle());
		$this->_metas[self::DESCRIPTION] = strip_tags($media->getDescription());
		$this->_metas[self::IMAGE] = $media->getThumbnailURI();
		$this->_metas[self::URL] = $media->getURI();
	}
	
	public function render()
	{
		$template = "<meta property=\"%s\" content=\"%s\" />".PHP_EOL;
		$ret = "";
		foreach($this->_metas as $k => $v) {
			$ret .= sprintf($template, $k, $v);
		}
		return $ret;
	}
}