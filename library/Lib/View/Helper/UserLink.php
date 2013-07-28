<?php
class Lib_View_Helper_UserLink extends Zend_View_Helper_Abstract
{
	public function userLink(User_Row $user, $class = 'user', $anonymous = false)
	{
		if($anonymous){
			$name = ucfirst($this->view->translate('anonymousUserName'));
			$title = ucfirst($name . ' - '.$this->view->translate('itemSing_user'));
			$content = "<span class=\"$class dataLink\" title=\"$title\">".$name.'</span>';
		} else {
			$name = $user->getTitle();
			$link = $user->getLink();
			
			$title = ucfirst($name . ' - '.$this->view->translate('itemSing_user'));
			$content = "<a class=\"$class dataLink\" title=\"$title\" href='".$link."'>".$name.'</a>';
		}
		
		return $content;
	}
}