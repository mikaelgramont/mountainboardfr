<?php
class VideoInfoParser
{
	const DAILYMOTION_IFRAME_SRC_REGEX = '/[\S]*https?:\/\/www\.dailymotion\.com\/embed\/video\/([a-zA-Z0-9_\-]*)[\S]*/i';
			
	const VIMEO_IFRAME_SRC_REGEX = '/[\S]*https?:\/\/player\.vimeo\.com\/video\/([0-9]*)[\S]*/i';
			
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
			return $this->_parseUrl($input);
		}
	}
	
	private function _parseIframe($el)
	{
		$src = $el->getAttribute('src');
		$matches = null;

		$matchCount = preg_match_all(self::YOUTUBE_IFRAME_SRC_REGEX, $src, $matches);
		if($matchCount == 1){
			$type = Media_Item_Video::SUBTYPE_YOUTUBE;
			$id = $matches[1][0];
			return new Media_Item_Video_ParsedObject($type, $id, $el->getAttribute('width'), $el->getAttribute('height'));
		}
		
		$matchCount = preg_match_all(self::VIMEO_IFRAME_SRC_REGEX, $src, $matches);
		if($matchCount == 1){
			$type = Media_Item_Video::SUBTYPE_VIMEO;
			$id = $matches[1][0];
			return new Media_Item_Video_ParsedObject($type, $id, $el->getAttribute('width'), $el->getAttribute('height'));
		}
		
		$matchCount = preg_match_all(self::DAILYMOTION_IFRAME_SRC_REGEX, $src, $matches);
		if($matchCount == 1){
			$type = Media_Item_Video::SUBTYPE_DAILYMOTION;
			$id = $matches[1][0];
			return new Media_Item_Video_ParsedObject($type, $id, $el->getAttribute('width'), $el->getAttribute('height'));
		}
		
		throw new Exception("Could not parse iframe from: " . $this->_input);
	}

	private function _parseUrl($el)
	{
		return new Media_Item_Video_ParsedObject(null, null, 0, 0);
	}
}