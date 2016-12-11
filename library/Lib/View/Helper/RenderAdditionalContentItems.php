<?php
class Lib_View_Helper_RenderAdditionalContentItems extends Zend_View_Helper_Abstract
{
	/**
	 * This view helper renders additional content for the different locations
	 * on the page.
	 *
	 * @return string
	 */
	public function renderAdditionalContentItems(User_Row $user, Lib_Acl $acl, $additionalContent, $contentLocation, $pageType = null)
	{
		switch($contentLocation){
			case Content::COLUMN2:
				$content = $this->_renderColumn2($user, $acl, $additionalContent);
				break;
			case Content::COLUMN23:
			case Content::COLUMN33:
				$content = $this->_renderColumn2($user, $acl, $additionalContent);
				break;
			default:
				$content = '';
				break;
		}

		return $content;
	}

	protected function _renderColumn2(User_Row $user, Lib_Acl $acl, $additionalContent)
	{
		$return = '';
		if(!$user->isLoggedIn()){
		    $return .= '<div class="card">'.PHP_EOL;
			$return .= '<p class="homePageLoginTitle">'.ucfirst($this->view->translate('homePageLogin')).'</p>'.PHP_EOL;
			$return .= $this->view->getHelper('actionLink')->loginRegistrationMarkup($user, false, true, $this->view->url(array(), 'newstuff'), true);
		    $return .= '</div>'.PHP_EOL;
		}

		if(isset($additionalContent['nextEvents']) && !empty($additionalContent['nextEvents'])){
			$return .= $this->_renderNextEvents($additionalContent['nextEvents'], $user);
		}

		if(isset($additionalContent['items']) && !empty($additionalContent['items'])){
			$return .= $this->_renderItems($additionalContent['items'], $user);
		}

		return $return;
	}

	protected function _renderNextEvents($events, User_Row $user)
	{
		$dataType = Event::ITEM_TYPE;
		$content  = "<div class=\"additionalContent $dataType\">".PHP_EOL;
		$next = count($events) > 1 ? $this->view->translate('nextEvents') : $this->view->translate('nextEvent');
		$content .= "	<p>".ucfirst($next).':</p>'.PHP_EOL;
		$content .= "	<ul class=\"nextEvents\">".PHP_EOL;

		$dateRenderer = new Lib_View_Helper_RenderData_Event();

		foreach($events as $event){
			$content .= '		<li>'.$this->view->itemLink($event).'<br/>'.$dateRenderer->renderDates($event).'</li>'.PHP_EOL;
    	}

    	$content .= '	</ul>'.PHP_EOL;
    	$content .= '</div>'.PHP_EOL;
    	return $content;
	}

	protected function _renderItems($items, User_Row $user)
	{
		$content = '';
		foreach($items as $item){
			$row = '';
			$dataType = $item->getItemType();
			switch($dataType){
				case Spot::ITEM_TYPE:
					$row .= '<p class="random">'.ucfirst($this->view->translate('random_'.$dataType)).':</p>'.PHP_EOL;
					$row .= $this->view->itemLink($item);
					$row .= ucfirst($this->view->translate('spotType')).': '.$item->getSpotType() . ', '.$this->view->translate('groundType').': '.$item->getGroundType();
					break;
				case Test::ITEM_TYPE:
					$row .= '<p class="random">'.ucfirst($this->view->translate('random_'.$dataType)).':</p>'.PHP_EOL;
					$row .= $this->view->itemLink($item);
					$row .= '<br/>'.PHP_EOL.$item->getDescription();
					break;
				case Media_Item::TYPE_PHOTO:
				case Media_Item::TYPE_VIDEO:
					$row .= $this->view->mediaThumbnail($item).'<br/>'.PHP_EOL;
					$row .= $this->view->itemLink($item);
					break;
				case Forum_Post::ITEM_TYPE:
					$row .= '<p class="random">'.ucfirst($this->view->translate('random_'.$dataType)).':</p>'.PHP_EOL;
					$topic = $item->getTopic();
					if(!$topic){
						continue;
					}
					$row .= $this->view->itemLink($topic);
					break;
				default:
					$row .= '<p>'.ucfirst($this->view->translate('random_'.$dataType)).':</p>'.PHP_EOL;
					$row .= $this->view->itemLink($item);
					break;
			}
			$content .= "<div class=\"card additionalContent $dataType\">".PHP_EOL;
			$content .= $row;
			$content .= '</div>'.PHP_EOL;
    	}
		return $content;
	}
}