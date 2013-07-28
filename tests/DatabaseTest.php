<?php
require_once('ApplicationTest.php');

class DatabaseTest extends ApplicationTest
{
    public function testDatabaseClass()
    {
        $this->dispatch('/');
        $this->assertTrue(Globals::getMainDatabase() instanceof Zend_Db_Adapter_Abstract );
    }
}