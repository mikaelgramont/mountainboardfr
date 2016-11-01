<?php
class Lib_View_Helper_ProfilePic extends Zend_View_Helper_Abstract
{
	public function profilePic(User_Row $user)
	{
		$avatar = $user->getAvatar($this->view->baseUrl);
		$avatar = $this->view->cdnHelper->url($avatar);
		$name = $user->getTitle();
		$link = $user->getLink();
		$return = <<<PIC
        <div class="avatar">
            <a href="$link"><img src="$avatar" alt="" title="$name" onerror="this.style.display='none'"></a>
        </div>
PIC;
		$return .= $this->view->userLink($user);
		return $return;
	}
}