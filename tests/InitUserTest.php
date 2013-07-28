<?php
require_once('ApplicationTest.php');

class InitUserTest extends ApplicationTest
{
    /**
     * User never logged in: default user
     */
    public function testDefaultUser()
    {
    	Zend_Auth::getInstance()->clearIdentity();
    	$this->dispatch('/');
        $this->assertEquals(0, Globals::getUser()->{User::COLUMN_USERID});
        $this->assertFalse(Zend_Auth::getInstance()->hasIdentity());
    }

    /**
     * User logging in: dummy
     */
    public function testPostLogin()
    {
    	Zend_Auth::getInstance()->clearIdentity();
    	$this->request
             ->setMethod('POST')
             ->setPost(array(
                 User::INPUT_USERNAME => 'dummy',
                 User::INPUT_PASSWORD => '123456789',
                 User::INPUT_LOGIN => '1'
        ));

        $this->dispatch('/');
        
        $this->assertTrue(Zend_Auth::getInstance()->hasIdentity());
        $this->assertEquals(1, Globals::getUser()->{User::COLUMN_USERID});
    }

    /**
     * User logged in in another session: dummy
     */
    public function testCookieLogin()
    {
		Zend_Auth::getInstance()->clearIdentity();
    	
    	// MD5 of 123456789 
		$this->request->setCookies(array(
            User::COOKIE_MD5 => strrev('25f9e794323b453885f5181f1b624d0b'),
            User::COOKIE_USERNAME => 'dummy',
            User::COOKIE_REMEMBER => '1'
        ));

        $this->dispatch('/login');

        $this->assertTrue(Zend_Auth::getInstance()->hasIdentity());
        $this->assertEquals(1, Globals::getUser()->{User::COLUMN_USERID});
    }

    /**
     * User logging out: dummy
     */
    public function testPostLogout()
    {
        $_SESSION['Zend_Auth'] = array(User::COLUMN_USERID=>1);

        $this->request
             ->setMethod('POST')
             ->setPost(array(
                 'userLO' => '1'
        ));

        $this->dispatch('/');

        $this->assertEquals(0, Globals::getUser()->{User::COLUMN_USERID});
        $this->assertFalse(Zend_Auth::getInstance()->hasIdentity());
    }

    /**
     * User failing to login as dummy
     *
     */
    public function testPostLoginFail()
    {
        $this->request
             ->setMethod('POST')
             ->setPost(array(
                 'userN' => 'dummy',
                 'userP' => '',
                 'userLI' => '1'
        ));

        $this->dispatch('/');

        $this->assertFalse(Zend_Auth::getInstance()->hasIdentity());
        $this->assertEquals(0, Globals::getUser()->{User::COLUMN_USERID});
    }
}