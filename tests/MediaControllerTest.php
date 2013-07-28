<?php
require_once('ApplicationTest.php');

class MediaControllerTest extends ApplicationTest
{	
	public function testDisplayPhotoAlbum()
	{
		$this->dispatch('/photos');
		
        $controller = new MediaController(
            $this->request,
            $this->response,
            $this->request->getParams()
        );
		
		$this->assertTrue($controller->view->album instanceof Media_Album_PhotoMain_Row);
		$this->assertEquals(Media_Album_PhotoMain::ID, $controller->view->album->getId());
	}

	public function testVideoAlbum()
	{
		$this->dispatch('/videos');
		
        $controller = new MediaController(
            $this->request,
            $this->response,
            $this->request->getParams()
        );
		
		$this->assertTrue($controller->view->album instanceof Media_Album_VideoMain_Row);
		$this->assertEquals(Media_Album_VideoMain::ID, $controller->view->album->getId());
	}

	public function testDisplayPortolio()
	{
		$this->dispatch('/portfolio');
		
        $controller = new MediaController(
            $this->request,
            $this->response,
            $this->request->getParams()
        );
		
		$this->assertTrue($controller->view->album instanceof Media_Album_Portfolio_Row);
        $this->assertEquals(Media_Album_Portfolio::ID, $controller->view->album->getId());
		$this->assertEquals(1, count($controller->view->comments));
	}

	public function testDisplayWrongAlbum()
	{
		$this->dispatch('/album/wrong/3055');
		$this->assertTrue(isset($this->request->error_handler));
		$this->assertTrue($this->request->error_handler->exception instanceof Lib_Exception);
	}

	/**
	 * This doesn't work because you can't test actual
	 * uploads with phpunit.
	 * The is_uploaded_file() function has no workaround.
	 */
	public function badTestUpload()
	{
		Globals::setUser($this->_getDummy());
		
		$_FILES = array(
			'media' => array(
				'name' => 'uploadBareges.jpg',
				'type' => 'image/jpeg',
				'tmp_name' => './files/bareges.jpg',
				'error' => '0',
				'size' => '93696',
			),
		);
		
		$this->request->setPost(array(
			'MAX_FILE_SIZE' => '4194304',
			'title' => 'Bareges',
			'description' => '360 method',
			'author' => 'paul taylor',
			'riders' => array(),
			'trick' => '',
			'spot' => '',
			'longitude' => '',
			'latitude' => '',
			'zoom' => '0',
			'yaw' => '',
			'pitch' => '',
			'mapType' => '0',
		));
		
		$this->dispatch('/envoyer/photo');
	}

	public function testUpload()
	{
		$photoTable = new Media_Item_Photo();
		$photos = $photoTable->find(20);
		$noResult = is_null($photos);
		$this->assertFalse($noResult);
		
	}
	
	public function testUploadJpg()
	{
		$this->inputFolder = "./files/";
/**
		$loginUrl = APP_URL.'/';
		$loginHttp = new Zend_Http_Client($loginUrl, array(
                        'maxredirects' => 5,
                        'timeout'      => 10
		));
		
		$loginHttp->setParameterPost(array(
                 User::INPUT_USERNAME => 'dummy',
                 User::INPUT_PASSWORD => '123456789',
                 User::INPUT_LOGIN => '1'
		));
		$response = $loginHttp->request('POST');
		Globals::getLogger()->info("Login response body\n".var_export($response->getBody(), true));
		//$cookies = $response->getHeader('Set-cookie');
		//Globals::getLogger()->info("cookies\n".var_export($cookies, true));
		//return;
*/
		$data = file_get_contents($this->inputFolder.'bareges.jpg');
		$title = 'testTitle '.date('Y-m-d H:i:s');
		
		$postUrl = APP_URL.'/envoyer/photo';
		Globals::getLogger()->info($postUrl);
		$postHttp = new Zend_Http_Client($postUrl);
		$postHttp->setParameterPost(array(
				'MAX_FILE_SIZE' => GLOBAL_UPLOAD_MAXSIZE,
				'title' => $title,
				'description' => 'testDescription',
				'author' => 'dummy',
				'riders' => array('dummy','writer'),
				'trick' => 'testTrick',
				'spot' => 'testSpot',
			 	'tags' => array('tag1', 'tag2'),
				'longitude' => '',
				'latitude' => '',
				'zoom' => '0',
				'yaw' => '',
				'pitch' => '',
				'mapType' => '0',
		));
		$postHttp->setFileUpload('bareges.jpg', 'media', $data);

		$postHttp->setCookie(User::COOKIE_MD5, strrev('25f9e794323b453885f5181f1b624d0b'));
		$postHttp->setCookie(User::COOKIE_USERNAME, 'dummy');
		$postHttp->setCookie(User::COOKIE_REMEMBER, '1');
		
		$response = $postHttp->request('POST'); 
		$body = $response->getBody();
		Globals::getLogger()->info(__METHOD__ .' - upload response body:'.PHP_EOL.$body);
		$this->assertContains('backToAlbum', $body);
	//	return;
		$textTable = new Data_TranslatedText();
		$title = strtolower($title);
		$text = $textTable->fetchRow("text = '$title'");
		
		$newPhotoId = $text->id;
		
		$photoTable = new Media_Item_Photo();
		$photos = $photoTable->find($newPhotoId);
		$noResult = is_null($photos);
		$this->assertFalse($noResult);
		$newPhoto = $photos->current();
		
		$this->assertEquals($newPhoto->getTitle(), $title);
	}
	
	private function _getDummy()
	{
		$dao = new User();
		$dummy = $dao->find(1)->current();
		return $dummy;
	}
	
	private function _getGuest()
	{
		$dao = new User_Guest();
		$guest = $dao->find(0)->current();
		return $guest;
	}
}