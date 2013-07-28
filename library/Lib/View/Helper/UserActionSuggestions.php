<?php
class Lib_View_Helper_UserActionSuggestions extends Zend_View_Helper_Abstract
{
	const TIME_SINCE_LAST_PHOTO = 2592000; //30 * 24 * 60 * 60

	public function userActionSuggestions(User_Row $user, $type)
	{
		$this->_user = $user;
		$suggestions = array();

		if(!$this->_user->hasLocation()){
			$suggestions[] = $this->_location();
		}

		if(empty($this->_user->avatar)){
			$suggestions[] = $this->_avatar();
		}

		$lastPostedPhoto = $this->_user->getLastPostedPhotoItem();
		if($lastPostedPhoto){
			if(strtotime($lastPostedPhoto->date) < (time() - self::TIME_SINCE_LAST_PHOTO)){
				$suggestions[] = $this->_postNewPhoto();
			}
		} else {
			$suggestions[] = $this->_postFirstPhoto();
		}

		if(empty($suggestions)){
			return '';
		}
		shuffle($suggestions);

		$return = '<p>'.ucfirst($this->view->translate('userActionSuggestion_header')).'</p>'.PHP_EOL;
		$return .= '<ul class="userActionSuggestions">'.PHP_EOL."\t<li class=\"actionLinkContainer\">".implode("</li>\n\t<li class=\"actionLinkContainer\">", $suggestions)."\t</li>".PHP_EOL.'</ul>'.PHP_EOL;
		return $return;
	}

	protected function _location()
	{
		$text = $this->view->translate('userActionSuggestion_Location_text');
		$link = $this->view->routeLink('userupdate', $this->view->translate('userActionSuggestion_Location_link'));
		$content = sprintf($text, $link);
		return $content;
	}

	protected function _avatar()
	{
		$text = $this->view->translate('userActionSuggestion_Avatar_text');
		$link = $this->view->routeLink('userupdate', $this->view->translate('userActionSuggestion_Avatar_link'));
		$content = sprintf($text, $link);
		return $content;
	}

	protected function _postNewPhoto()
	{
		$text = $this->view->translate('userActionSuggestion_NewPhoto_text');
		$link = $this->view->routeLink('uploadphotomain', $this->view->translate('userActionSuggestion_NewPhoto_link'));
		$content = sprintf($text, $link);
		return $content;
	}

	protected function _postFirstPhoto()
	{
		$text = $this->view->translate('userActionSuggestion_FirstPhoto_text');
		$link = $this->view->routeLink('uploadphotomain', $this->view->translate('userActionSuggestion_FirstPhoto_link'));
		$content = sprintf($text, $link);
		return $content;
	}

}
