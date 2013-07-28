<?php
class Lib_Controller_Helper_Examplehelper extends Zend_Controller_Action_Helper_Abstract
{
    public function doAdd($a, $b)
    {
        return $a + $b;
    }

    public function direct($a, $b = 3)
    {
        return $this->doAdd($a, $b);
    }
}