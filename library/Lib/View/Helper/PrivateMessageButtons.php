<?php
class Lib_View_Helper_PrivateMessageButtons extends Zend_View_Helper_Abstract
{
	public function privateMessageButtons($type = 'home')
	{
		$content = '';
		$content .= '<ul id="privateMessagesMenu">'.PHP_EOL;
		if($type != 'home'){
			$content .= '	<li>'.$this->view->routeLink('privatemessageshome','home').'</li>'.PHP_EOL;
		}
		if($type != 'sent'){
			$content .= '	<li>'.$this->view->routeLink('privatemessagessent','sent messages').'</li>'.PHP_EOL;
		}
		if($type != 'new'){
			$content .= '	<li>'.$this->view->routeLink('privatemessagesnew','new').'</li>'.PHP_EOL;
		}
		$content .= '</ul>'.PHP_EOL;
		$content .= '<div style="clear:both"></div>'.PHP_EOL;
		return $content;		
	}
}