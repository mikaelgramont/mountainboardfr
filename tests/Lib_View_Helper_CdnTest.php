<?php
require_once('ApplicationTest.php');

class Lib_View_Helper_CdnTest extends ApplicationTest
{
	public $cdn;
	public $site;

	public function setup()
	{
		parent::setup();

		$this->cdn = 'http://cdn.com/';
		$this->jsCdn = 'http://js.cdn.com/';
		$this->cssCdn = 'http://css.cdn.com/';

		$this->site = 'http://www.site.com/';

		$this->view = new Zend_View();
		$this->helper = new Lib_View_Helper_Cdn($this->view);
		$this->helper->setCdnUrl($this->cdn);
		$this->helper->setJsCdnUrl($this->jsCdn);
		$this->helper->setCssCdnUrl($this->cssCdn);

		$this->helper->setSiteUrl($this->site);
	}

	public function testInstance()
	{
		$this->assertTrue($this->helper instanceof Lib_View_Helper_Cdn);
	}

	/**
	 * @testdox It should use the default domain url in development environment
	 */
	public function testUrlDevEnv()
	{
		$this->helper->setDevMode();

		$dest = $this->helper->url($this->site);
		$this->assertEquals($this->site, $dest, 'Url was unexpectedly modified while in dev mode');
	}

	/**
	 * @testdox It should use the cdn domain url in production environment
	 */
	public function testUrlProdEnv()
	{
		$this->helper->setProdMode();

		$dest = $this->helper->url($this->site);
		$this->assertEquals($this->cdn, $dest, 'Url was incorrectly processed while in prod mode');
	}

	/**
	 * @testdox It should change the url so that it points to the cdn domain
	 * @dataProvider urlDataProvider
	 */
	public function testUrl($url, $expected)
	{
		$actual = $this->helper->url($url);
		$this->assertEquals($expected, $actual);
	}

	public function urlDataProvider()
	{
		return array(
			'relative path' => array(
				'/images/temp/file.small.jpg',
				'http://cdn.com/images/temp/file.small.jpg'
			),
			'absolute path' => array(
				'http://www.site.com/images/temp/file.small.jpg',
				'http://cdn.com/images/temp/file.small.jpg'
			),
		);
	}

	/**
	 * @testdox It should change the url so that it points to the cdn domain
	 * @dataProvider cssUrlDataProvider
	 */
	public function testCssUrl($url, $expected)
	{
		$actual = $this->helper->cssUrl($url);
		$this->assertEquals($expected, $actual);
	}

	public function cssUrlDataProvider()
	{
		return array(
			'relative path' => array(
				'/css/temp/file.small.css',
				'http://css.cdn.com/css/temp/file.small.css'
			),
			'absolute path' => array(
				'http://www.site.com/css/temp/file.small.css',
				'http://css.cdn.com/css/temp/file.small.css'
			),
		);
	}

	/**
	 * @testdox It should change the url so that it points to the cdn domain
	 * @dataProvider jsUrlDataProvider
	 */
	public function testJsUrl($url, $expected)
	{
		$actual = $this->helper->jsUrl($url);
		$this->assertEquals($expected, $actual);
	}

	public function jsUrlDataProvider()
	{
		return array(
			'relative path' => array(
				'/js/temp/file.small.js',
				'http://js.cdn.com/js/temp/file.small.js'
			),
			'absolute path' => array(
				'http://www.site.com/js/temp/file.small.js',
				'http://js.cdn.com/js/temp/file.small.js'
			),
		);
	}

	/**
	 * @testdox It should replace urls to the cdn domain
	 */
	public function testReplace()
	{
		$in = <<<HTML
		<a href="http://www.site.com/images/temp/file.small.jpg">bla</a>
		<a href="/images/temp/file.small.jpg">bla</a>
		<a href="./images/temp/file.small.jpg">bla</a>
		<a href="../images/temp/file.small.jpg">bla</a>
		<a href="../../images/temp/file.small.jpg">bla</a>
		<a href="../../../images/temp/file.small.jpg">bla</a>
		<img src="http://www.site.com/images/temp/file.small.jpg"/>
		<img src="../../../images/temp/file.small.jpg"/>
HTML;
		$out = <<<HTML
		<a href="http://cdn.com/images/temp/file.small.jpg">bla</a>
		<a href="http://cdn.com/images/temp/file.small.jpg">bla</a>
		<a href="http://cdn.com/images/temp/file.small.jpg">bla</a>
		<a href="http://cdn.com/images/temp/file.small.jpg">bla</a>
		<a href="http://cdn.com/images/temp/file.small.jpg">bla</a>
		<a href="http://cdn.com/images/temp/file.small.jpg">bla</a>
		<img src="http://cdn.com/images/temp/file.small.jpg"/>
		<img src="http://cdn.com/images/temp/file.small.jpg"/>
HTML;

		$actual = $this->helper->replace($in);
		$this->assertEquals($out, $actual);
	}
}

