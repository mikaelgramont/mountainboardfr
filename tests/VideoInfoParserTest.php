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
	
	public function testYouTubePageUrl()
	{
		$input = "https://www.youtube.com/watch?v=32FB-gYr49Y";
		$expected = array(Media_Item_Video::SUBTYPE_YOUTUBE, "32FB-gYr49Y");
		$this->_assertParse($input, $expected);
	}

	public function testYouTubeShortenedUrl()
	{
		$input = "https://youtu.be/32FB-gYr49Y";
		$expected = array(Media_Item_Video::SUBTYPE_YOUTUBE, "32FB-gYr49Y");
		$this->_assertParse($input, $expected);
		}
	
	public function testYouTubeEmbedCode()
	{
		$input = <<<HTML
		<iframe width="560" height="315" src="https://www.youtube.com/embed/32FB-gYr49Y" frameborder="0" allowfullscreen></iframe>	
HTML;
		$expected = array(Media_Item_Video::SUBTYPE_YOUTUBE, "32FB-gYr49Y", 560, 315);
		$this->_assertParse($input, $expected);
	}
}

// /usr/local/php5/bin/php /usr/local/bin/phpunit VideoInfoParserTest.php
// Try parsing the input as HTML. If only one text node, parse it with regexes. Otherwise try to find relevant bits.