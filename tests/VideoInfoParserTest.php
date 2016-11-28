<?php
require_once('ApplicationTest.php');

/**
 * Tests that the VideoInfoParser sends back the right Media_Item_Video_ParsedObject objects.
 * 
 * Run /usr/local/php5/bin/php /usr/local/bin/phpunit VideoInfoParserTest.php
 * from the tests folder. 
 */
class VideoInfoParserTest extends ApplicationTest
{
	public function setup()
	{
		parent::setup();
		$this->parser = new VideoInfoParser();
	}
	
	public function testDailyMotionIframeCode()
	{
		$input = <<<HTML
		<iframe width="560" height="315" src="https://www.dailymotion.com/embed/video/x1buew_ouch_sport" frameborder="0" allowfullscreen></iframe>
HTML;
		$expected = new Media_Item_Video_ParsedObject(Media_Item_Video::SUBTYPE_DAILYMOTION, "x1buew_ouch_sport", 560, 315);
		$this->_assertParse($input, $expected);
	}
	
	public function testDailyMotionPageCode()
	{
		$input = <<<HTML
		some bs https://www.dailymotion.com/video/x1buew_ouch_sport somebs
HTML;
		$expected = new Media_Item_Video_ParsedObject(Media_Item_Video::SUBTYPE_DAILYMOTION, "x1buew_ouch_sport", 480, 270);
		$this->_assertParse($input, $expected);
	}
	
	public function testVimeoIframeCode()
	{
		$input = <<<HTML
		<iframe width="560" height="315" src="https://player.vimeo.com/video/140076841" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
HTML;
		$expected = new Media_Item_Video_ParsedObject(Media_Item_Video::SUBTYPE_VIMEO, "140076841", 560, 315);
		$this->_assertParse($input, $expected);
	}
	
	public function testVimeoPageCode()
	{
		$input = <<<HTML
		some bs https://vimeo.com/140076841 some bs
HTML;
		$expected = new Media_Item_Video_ParsedObject(Media_Item_Video::SUBTYPE_VIMEO, "140076841", 640, 360);
		$this->_assertParse($input, $expected);
	}
	
	public function testYouTubeIframeCode()
	{
		$input = <<<HTML
		<iframe width="560" height="315" src="https://www.youtube.com/embed/32FB-gYr49Y" frameborder="0" allowfullscreen></iframe>
HTML;
		$expected = new Media_Item_Video_ParsedObject(Media_Item_Video::SUBTYPE_YOUTUBE, "32FB-gYr49Y", 560, 315);
		$this->_assertParse($input, $expected);
	}
	
	public function testYouTubePageUrl()
	{
		$input = "https://www.youtube.com/watch?v=32FB-gYr49Y";
		$expected = new Media_Item_Video_ParsedObject(Media_Item_Video::SUBTYPE_YOUTUBE, "32FB-gYr49Y");
		$this->_assertParse($input, $expected);
	}
	
	public function testYouTubeShortUrl()
	{
		$input = "https://youtu.be/32FB-gYr49Y";
		$expected = new Media_Item_Video_ParsedObject(Media_Item_Video::SUBTYPE_YOUTUBE, "32FB-gYr49Y");
		$this->_assertParse($input, $expected);
	}
	
	private function _assertParse($input, $expected)
	{
		$this->assertEquals($expected, $this->parser->parse($input));
	}
	
}