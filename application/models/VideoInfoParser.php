<?php
/**
 * Builds a Media_Item_Video_ParsedObject out of user input.
 * Handles page urls as well as embed codes.
 */
class VideoInfoParser
{
	const DAILYMOTION_IFRAME_SRC_REGEX = '/[\S]*https?:\/\/www\.dailymotion\.com\/embed\/video\/([a-zA-Z0-9_\-]*)[\S]*/i';
	const DAILYMOTION_PAGE_URL_REGEX = '/[\S]*https?:\/\/www\.dailymotion\.com\/video\/([a-zA-Z0-9_\-]*)[\S]*/i';
	
	const VIMEO_IFRAME_SRC_REGEX = '/[\S]*https?:\/\/player\.vimeo\.com\/video\/([0-9]*)[\S]*/i';
	const VIMEO_PAGE_URL_REGEX = '/[\S]*https?:\/\/vimeo\.com\/([0-9]*)[\S]*/i';
	
	const YOUTUBE_IFRAME_SRC_REGEX = '/[\S]*https?:\/\/www\.youtube\.com\/embed\/([a-zA-Z0-9_\-]*)[\S]*/i';
	const YOUTUBE_PAGE_URL_REGEX = '/[\S]*https?:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_\-]*)[\S]*/i';
	const YOUTUBE_SHORT_URL_REGEX = '/[\S]*https?:\/\/youtu\.be\/([a-zA-Z0-9_\-]*)[\S]*/i';
	
	public function parse($input)
	{
		$this->_input = trim($input);
		
		$dom = new SimpleHtmlDom();
		$dom->load($this->_input);
		
		$iframes = $dom->find('iframe');
		if(sizeof($iframes) > 0) {
			$iframe = $iframes[0];
			return $this->_parseIframe($iframe);
		} else {
			return $this->_parseUrl();
		}
	}
	
	public function isValid($input)
	{
		try {
			$this->parse($input);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	private function _getRegexes()
	{
		$regexes = array();
		$regexes[self::DAILYMOTION_IFRAME_SRC_REGEX] = Media_Item_Video::SUBTYPE_DAILYMOTION;
		$regexes[self::DAILYMOTION_PAGE_URL_REGEX] = Media_Item_Video::SUBTYPE_DAILYMOTION;
		
		$regexes[self::VIMEO_IFRAME_SRC_REGEX] = Media_Item_Video::SUBTYPE_VIMEO;
		$regexes[self::VIMEO_PAGE_URL_REGEX] = Media_Item_Video::SUBTYPE_VIMEO;
		
		$regexes[self::YOUTUBE_IFRAME_SRC_REGEX] = Media_Item_Video::SUBTYPE_YOUTUBE;
		$regexes[self::YOUTUBE_PAGE_URL_REGEX] = Media_Item_Video::SUBTYPE_YOUTUBE;
		$regexes[self::YOUTUBE_SHORT_URL_REGEX] = Media_Item_Video::SUBTYPE_YOUTUBE;
		
		return $regexes;
	}

	private function _parseIframe($el)
	{
		$src = $el->getAttribute('src');
		
		foreach($this->_getRegexes() as $regex => $type) {
			$matches = null;
			$matchCount = preg_match_all($regex, $src, $matches);
			if($matchCount == 1){
				$id = $matches[1][0];
				return new Media_Item_Video_ParsedObject($type, $id, $el->getAttribute('width'), $el->getAttribute('height'));
			}
		}
				
		throw new Exception("Could not parse iframe from: " . $this->_input);
	}

	private function _parseUrl()
	{
		foreach($this->_getRegexes() as $regex => $type) {
			$matches = null;
			$matchCount = preg_match_all($regex, $this->_input, $matches);
			if($matchCount == 1){
				$id = $matches[1][0];
				return new Media_Item_Video_ParsedObject($type, $id);
			}
		}
		
		throw new Exception("Could not parse: " . $this->_input);
	}
}