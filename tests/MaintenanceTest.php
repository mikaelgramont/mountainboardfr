<?php
require_once('ApplicationTest.php');

class MaintenanceTest extends ApplicationTest
{
    public function testMaintenanceOff()
    {
        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');
    }
}