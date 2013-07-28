<?php
class User_Guest_Row extends User_Row
{
	public function getTitle()
	{
		$title = Globals::getTranslate()->translate('guest');
		return $title;
	}

	public function getUploadFolder()
	{
		throw new Lib_Exception("Guest users cannot be asked for their upload folder");
	}

	public function getDefaultNotifications()
	{
		throw new Lib_Exception("Guest users cannot be asked for default notifications");
	}
}