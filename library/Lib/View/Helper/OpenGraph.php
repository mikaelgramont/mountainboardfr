<?php
class Lib_View_Helper_OpenGraph extends Zend_View_Helper_Abstract
{
	const DESCRIPTION = "og:description";
	const IMAGE = "og:image";
	const TITLE = "og:title";
	const URL = "og:url";
	
	protected $_metas = array();
	
	public function openGraph()
	{
		return $this;
	}
	
	protected function defaultBits_(Data_Row $data)
	{
		$this->_metas[self::TITLE] = ucfirst(strip_tags($data->getTitle()));
		$this->_metas[self::DESCRIPTION] = ucfirst(strip_tags(
				$data->getDescription()));
		$this->_metas[self::URL] = APP_URL.$data->getLink();
	}
	
	public function forMedia(Media_Item_Row $media)
	{
		$this->defaultBits_($media);
		$this->_metas[self::IMAGE] = $media->getThumbnailURI();
	}
	
	public function forArticle($article)
	{
		$this->defaultBits_($article);
	}
	
	public function render()
	{
		$template = "<meta property=\"%s\" content=\"%s\" />";
		$bits = array();
		foreach($this->_metas as $k => $v) {
			$bits[] = sprintf($template, $k, $v);
		}
		return implode($bits, PHP_EOL);
	}
}