<?php
require_once('ApplicationTest.php');

class VideoInfoParserTest extends ApplicationTest
{
	public function setup()
	{
		parent::setup();
		$this->parser = new VideoInfoParser();
	}
	
	private function _assertParse($input, $expected)
	{
		$this->assertEquals($expected, $this->parser->parse($input));
	}
	
	public function _testYouTubePageUrl()
	{
		$input = "https://www.youtube.com/watch?v=32FB-gYr49Y";
		$expected = new Media_Item_Video_ParsedObject(Media_Item_Video::SUBTYPE_YOUTUBE, "32FB-gYr49Y");
		$this->_assertParse($input, $expected);
	}

	public function _testYouTubeShortenedUrl()
	{
		$input = "https://youtu.be/32FB-gYr49Y";
		$expected = new Media_Item_Video_ParsedObject(Media_Item_Video::SUBTYPE_YOUTUBE, "32FB-gYr49Y");
		$this->_assertParse($input, $expected);
		}
	
	public function testYouTubeEmbedCode()
	{
		$input = <<<HTML
		<iframe width="560" height="315" src="https://www.youtube.com/embed/32FB-gYr49Y" frameborder="0" allowfullscreen></iframe>	
HTML;
		$expected = new Media_Item_Video_ParsedObject(Media_Item_Video::SUBTYPE_YOUTUBE, "32FB-gYr49Y", 560, 315);
		$this->_assertParse($input, $expected);
	}

	public function testVimeoEmbedCode()
	{
		$input = <<<HTML
		<iframe width="560" height="315" src="https://player.vimeo.com/video/140076841" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
HTML;
		$expected = new Media_Item_Video_ParsedObject(Media_Item_Video::SUBTYPE_VIMEO, "140076841", 560, 315);
		$this->_assertParse($input, $expected);
	}
	
	public function testDailyMotionEmbedCode()
	{
		$input = <<<HTML
		<iframe width="560" height="315" src="https://www.dailymotion.com/embed/video/x1buew_ouch_sport" frameborder="0" allowfullscreen></iframe>
HTML;
		$expected = new Media_Item_Video_ParsedObject(Media_Item_Video::SUBTYPE_DAILYMOTION, "x1buew_ouch_sport", 560, 315);
		$this->_assertParse($input, $expected);
	}
}

// /usr/local/php5/bin/php /usr/local/bin/phpunit VideoInfoParserTest.php
// Try parsing the input as HTML. If only one text node, parse it with regexes. Otherwise try to find relevant bits.