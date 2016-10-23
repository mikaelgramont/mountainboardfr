<?php
class Lib_View_Helper_Cdn extends Zend_View_Helper_Abstract
{
	const DEV = 'dev';
	const PROD = 'prod';

	protected $_cdnUrl;
	protected $_cssCdnUrl;
	protected $_imgCdnUrl;
	protected $_jsCdnUrl;
	protected $_siteUrl;
	protected $_mode;

	public function cdn()
	{
		return $this;
	}

	public function setCdnUrl($url)
	{
		$this->_cdnUrl = $url;
	}

	public function setCssCdnUrl($url)
	{
		$this->_cssCdnUrl = $url;
	}

        public function setImgCdnUrl($url)
        {
                $this->_imgCdnUrl = $url;
        }

	public function setJsCdnUrl($url)
	{
		$this->_jsCdnUrl = $url;
	}

	public function setSiteUrl($url)
	{
		$this->_siteUrl = $url;
	}

	public function setDevMode()
	{
		$this->_mode = self::DEV;
	}

	public function setProdMode()
	{
		$this->_mode = self::PROD;
	}

	public function replace($content)
	{
		if($this->_mode == self::DEV){
			//return $content;
		}

		$dom = new SimpleHtmlDom();
		$dom->load($content);

		$types = array(
			array($dom->find('a'), 'href'),
			array($dom->find('img'), 'src'),
			array($dom->find('iframe'), 'src'),
		);

		foreach($types as $type){
			$elements = $type[0];
			$attr = $type[1];

			foreach($elements as $element){
				if(!isset($element->$attr)) {
					continue;
				}


				if(strpos($element->$attr, $this->_siteUrl) === 0){
					$element->$attr = str_replace($this->_siteUrl, $this->_cdnUrl, $element->$attr);
					continue;
				}
				if(in_array(substr($element->$attr, 0, 1), array('.', '/'))){
					$pattern = '/^(\/|(.\/)|(..\/)*)/';
					$element->$attr = preg_replace($pattern, $this->_cdnUrl, $element->$attr);
				}
			}

		}

		$output = $dom->save();
		return $output;
	}

	public function url($assetPath, $cdnUrl = null)
	{
		if($this->_mode == self::DEV){
			return $assetPath;
		}

		if(!$cdnUrl){
			$cdnUrl = $this->_cdnUrl;
		}
		$pattern = "/^(".str_replace('/', '\/', $this->_siteUrl).'|\/)(.*)$/';
		$return = preg_replace($pattern, $cdnUrl.'${2}', $assetPath);
		return $return;
	}

	public function cssUrl($assetPath)
	{
		$return = $this->url($assetPath, $this->_cssCdnUrl);
		return $return;
	}

        public function imgUrl($assetPath)
        {
                $return = $this->url($assetPath, $this->_imgCdnUrl);
                return $return;
        }

	public function jsUrl($assetPath)
	{
		$return = $this->url($assetPath, $this->_jsCdnUrl);
		return $return;
	}
}
