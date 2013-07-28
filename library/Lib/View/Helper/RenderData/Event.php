<?php
class Lib_View_Helper_RenderData_Event extends Zend_View_Helper_Abstract
{
	/**
	 * @var Event_Row
	 */
	protected $_event;

	public function renderData_Event(Event_Row $event, Media_Album_Row $album = null)
	{
        $this->_event = $event;


        $content  = '<div class="eventInfo">'.PHP_EOL;
        $content .= $this->_renderTitle();
		$content .= $this->_renderOrganisationInfo();
		$content .= '</div>'.PHP_EOL;
        $content .= '<p class="eventDates">'.$this->renderDates($this->_event).'</p>';
        //$content .= $this->view->shareButtons(APP_URL.$this->view->url()).PHP_EOL;
		$content .= '<div class="clear"></div>'.PHP_EOL;

        $content .= $this->view->renderTags($this->_event->getTags());
        $content .= $this->view->shareButtons()->all(APP_URL.$this->view->url(), 'horizontal').PHP_EOL;

        $content .= $this->_renderDescription();
        $content .= $this->_renderContent();

        if(!empty($album)){
			$content .= $this->view->itemLink($album);
			$content .= $this->view->albumPreview($album);
        }

        return $content;
	}

	protected function _renderTitle()
	{
		$content = '<h1 class="eventTitle">'.ucfirst($this->_event->getTitle());
        if(($this->_event->isEditableBy($this->view->user, $this->view->acl))){
            $content .= $this->view->itemStatus($this->_event, true);
            $content .= $this->view->editLink($this->_event);
        }
        if(($this->_event->isDeletableBy($this->view->user, $this->view->acl))){
            $content .= $this->view->deleteLink($this->_event);
        }
		$content .= '</h1>'.PHP_EOL;

        return $content;
	}

	public function renderDates($event)
	{
		if($event->startDate == '0000-00-00' && $event->endDate == '0000-00-00'){
			/**
			 * @todo: log empty dates
			 */
			return '';
		} elseif(empty($event->endDate) || $event->endDate == '0000-00-00' || $event->endDate == $event->startDate){
        	$content  = '<span class="singleDay">'.Lib_Date::getFormattedDate($event->startDate, false, true).'</span>'.PHP_EOL;
		} else {

			$dates = $this->_joinDates(array(
				$event->startDate,
				$event->endDate,
			), Zend_Registry::get('Zend_Locale'));

			$content  = '<span class="severalDays">'.$dates.'</span>'.PHP_EOL;
		}

        return $content;
	}

	protected function _formatDate($date)
	{
		return ;
	}

	protected function _joinDates(array $dates, $locale = null)
	{
		/**
		 * Check whether the month of all dates is the same
		 * If yes, enumerate days but group month and year.
		 */
		$translate = Globals::getTranslate();

		$months = $days = array();
		foreach($dates as $date){
			$parts = explode('-', $date);
			$months[$parts[1]] = $month = $parts[1];
			$days[] = $parts[2];
		}

		if(count($months)  == 1) {
			/**
			 * Join $days with ' and '
			 */
			 switch($locale){
	            case 'en':
	                $joined = Lib_Date::getTranslatedMonth($month, $translate).' ';
	                $lastDay = count($days) - 1;
	                $dayChain = '';
	                foreach($days as $index => $day){
	                	if($index == 0){
	                		$dayChain = Lib_Date::getOrdinalDay($day);
	                	} elseif($index == $lastDay) {
	                		$dayChain .= ' '.$translate->translate('and').' '.Lib_Date::getOrdinalDay($day);
	                	} else {
	                		$dayChain .= ', '.Lib_Date::getOrdinalDay($day);
	                	}
	                }

	                $joined .= $dayChain.' '.$parts[0];
	                break;
	            case 'fr':
	            default:
	                $joined = implode(' '.$translate->translate('and').' ', $days).' '.Lib_Date::getTranslatedMonth($month, $translate).' '.$parts[0];
	                break;
	        }


		} else {
			/**
			 * Different months
			 * Loop on dates, join day and month individually, use one year
			 */
			$joined = implode(' - ', array(Lib_Date::getFormattedDate($dates[0], false), Lib_Date::getFormattedDate($dates[1], false)));
		}

		return $joined;
	}

	protected function _renderOrganisationInfo()
	{
		$content = '';
		$hasAuthor = $this->_event->hasAuthor();
		$hasSpot = $this->_event->hasSpot();

		if($hasAuthor && $hasSpot){
			$content .= '<p class="eventOrganisation">';
			$content .= ucfirst($this->view->translate('organisedBy')) . ' ' . $this->view->userLink($this->_event->getAuthor());
			$content .= ' '.$this->view->translate('at') . ' ' . $this->view->itemLink($this->_event->getSpot());
			$content .= '</p>'.PHP_EOL;
		} elseif ($hasAuthor) {
			$content .= '<p class="eventOrganisation">';
			$content .= ucfirst($this->view->translate('organisedBy')) . ': ' . $this->view->userLink($this->_event->getAuthor());
			$content .= '</p>'.PHP_EOL;

		} elseif ($hasSpot) {
			$content .= '<p class="eventOrganisation">';
			$content .= ucfirst($this->view->translate('spot')) . ': ' . $this->view->itemLink($this->_event->getSpot());
			$content .= '</p>'.PHP_EOL;

		} else {
			$content = '';
		}

		$content .= $this->_renderType().PHP_EOL;

		return $content;
	}

	protected function _renderType()
	{
		$content = '';

		$types = array_keys(Lib_Form_Element_Event_Type::$types);
		if(!isset($types[$this->_event->type])){
			/**
			 * @todo: log
			 */
			return $content;
		}

		$type = $types[$this->_event->type];


		if($type == Lib_Form_Element_Event_Type::COMPETITION) {
			if($this->_event->compLevel) {
				$content .= ucfirst($this->view->translate('eventType')).': ';
				$content .= $this->_renderCompLevel().PHP_EOL;
			}
			$content .= $this->_renderCompContent().PHP_EOL;
		} else {
			$content .= ucfirst($this->view->translate('eventType')).': ';
			$content .= $this->view->translate($type);
		}

		$content = '<p class="eventType">'.$content.'</p>';
		return $content;
	}


	protected function _renderCompContent()
	{
		$constants = Lib_Form_Element_Event_CompContent::$compContent;
		$content  = ucfirst($this->view->translate('compContent')).': ';
		$content .= $this->view->collapsedValues($this->_event->compContent, $constants);
		$content = '<p class="eventCompContent">'.$content.'</p>';
		return $content;
	}

	protected function _renderCompLevel()
	{
		$constants = Lib_Form_Element_Event_CompLevel::$compLevel;
		$return = $this->view->collapsedValues($this->_event->compLevel, $constants);
		return $return;
	}

	protected function _renderDescription()
	{
        $content  = '<p class="description">'.$this->_event->getDescription().'</p>'.PHP_EOL;
        $content .= '<div class="clear"></div>'.PHP_EOL;
        return $content;
	}

	protected function _renderContent()
	{
        $content = $this->_event->getContentFromCdn($this->view->cdnHelper).PHP_EOL;
		return $content;
	}
}
