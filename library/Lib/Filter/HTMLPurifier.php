<?php
class Lib_Filter_HTMLPurifier implements Zend_Filter_Interface
{
	public function filter($value)
	{
		$purifier = Globals::getHTMLPurifier();
		$purified = $purifier->purify($value);
		return $purified;
	}
}