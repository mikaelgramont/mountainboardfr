<?php
require_once('ApplicationTest.php');

class File_PhotoTest extends ApplicationTest
{	
    protected function setUp()
    {
		parent::setUp();
    			
        $this->inputFolder = "./files/";
        $this->outputFolder = "./tmp/";
    }

	protected function tearDown()
	{
		$command = "rm -f $this->outputFolder/*";
		shell_exec($command);
		parent::tearDown();
	}

	public function testOpenJpg()
	{
		$file = "Upload_1000x1000_JPG.jpg";
		$obj = new File_Photo($this->inputFolder.$file);
		$this->assertEquals($file, $obj->getName());
	}

	public function testOpenPng()
	{
		$file = "Upload_1000x1000_PNG.png";
		$obj = new File_Photo($this->inputFolder.$file);
		$this->assertEquals($file, $obj->getName());
	}

	public function testOpenGif()
	{
		$file = "Upload_1000x1000_GIF.gif";
		$obj = new File_Photo($this->inputFolder.$file);
		$this->assertEquals($file, $obj->getName());
	}
	
	public function testResizeJpg()
	{
		$file = "Upload_1000x1000_JPG.jpg";
		$obj = new File_Photo($this->inputFolder.$file);
		
		$newName = "ResizeJpg_600x600_JPG.jpg";
		$copy = $obj->resizeTo(600, 600, $this->outputFolder.$newName);
		$this->assertEquals($this->outputFolder.$newName, $copy->getFullPath());
		
		list($x, $y) = getimagesize($this->outputFolder.$newName);
		$this->assertEquals(600, $x);
		$this->assertEquals(600, $y);
	}

	public function testResizePng()
	{
		$file = "Upload_1000x1000_PNG.png";
		$obj = new File_Photo($this->inputFolder.$file);
		
		$newName = "ResizePng_600x600_PNG.png";
		$copy = $obj->resizeTo(600, 600, $this->outputFolder.$newName);
		$this->assertEquals($this->outputFolder.$newName, $copy->getFullPath());
		
		list($x, $y) = getimagesize($this->outputFolder.$newName);
		$this->assertEquals(600, $x);
		$this->assertEquals(600, $y);
	}

	public function testResizeGif()
	{
		$file = "Upload_1000x1000_GIF.gif";
		$obj = new File_Photo($this->inputFolder.$file);
		
		$newName = "Upload_600x600_GIF.gif";
		$copy = $obj->resizeTo(600, 600, $this->outputFolder.$newName);
		$this->assertEquals($this->outputFolder.$newName, $copy->getFullPath());
		
		list($x, $y) = getimagesize($this->outputFolder.$newName);
		$this->assertEquals(600, $x);
		$this->assertEquals(600, $y);
	}

	public function testPreserveRatio()
	{
		$file = "Upload_1000x1000_JPG.jpg";
		$obj = new File_Photo($this->inputFolder.$file);
		
		$newName = "Upload_200x200_JPG.jpg";
		$copy = $obj->resizeTo(600, 200, $this->outputFolder.$newName);
		$this->assertEquals($this->outputFolder.$newName, $copy->getFullPath());
		
		list($x, $y) = getimagesize($this->outputFolder.$newName);
		$this->assertEquals(200, $x);
		$this->assertEquals(200, $y);
	}

	public function testCreateThumbnail()
	{
		$file = "Upload_1000x1000_JPG.jpg";
		$obj = new File_Photo($this->inputFolder.$file);
		
		$thumbnail = $obj->createThumbnail($this->outputFolder, 200, 100);
		
		list($x, $y) = getimagesize($this->outputFolder.$file);
		$this->assertEquals(100, $x);
		$this->assertEquals(100, $y);
	}

	public function testLimitDimensions()
	{
		/*
		$originalFileName = "Upload_1000x1000_JPG.jpg";
		$originalFile = new File_Photo($this->inputFolder.$originalFileName);
		
		$obj = $originalFile->copy($this->outputFolder.$originalFileName);
		$obj->limitDimensions(300, 300);
		
		list($x, $y) = getimagesize($obj->getFullPath());
		$this->assertEquals(300, $x);
		$this->assertEquals(300, $y);
		*/
	}
}

