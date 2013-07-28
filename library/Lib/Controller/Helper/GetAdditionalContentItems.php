<?php
class Lib_Controller_Helper_GetAdditionalContentItems extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Builds the additionnal content that will be displayed along
	 * the main content
	 *
	 */
	public function homePage(User_Row $user, Lib_Acl $acl)
	{
		$additionalContent = $this->_getHomePageColumn2Content($user, $acl);
		return $additionalContent;
	}

	/**
	 * Builds the additionnal content that will be displayed along
	 * the main content
	 *
	 */
	public function direct(User_Row $user, Lib_Acl $acl)
	{
		$types = array(
			Spot::ITEM_TYPE,
    		Test::ITEM_TYPE,
    		Dossier::ITEM_TYPE,
    		Blog_Post::ITEM_TYPE,
    		Media_Item::TYPE_PHOTO,
    		Media_Item::TYPE_VIDEO,
		);

		shuffle($types);
		$items = $this->_getRandomItems($types, $user, $acl);
		return array(
			'items' => $items,
		);
	}

	protected function _getHomePageColumn2Content(User_Row $user, Lib_Acl $acl)
	{
		/**
		 * 2011-05-17 added the events poster on the home page
		 */
		$poster = null;
		$table = new Media_Item_Photo();
		$res = null; //$table->find(1581);
		if($res){
			$poster= $res->current();
		}

		$types = array(
    		Media_Item::TYPE_PHOTO,
    		Media_Item::TYPE_VIDEO,
		);
		$items = $this->_getRandomItems($types, $user, $acl);
		if($poster){
			array_unshift($items, $poster);
		}

        $table = new Event();
        $nextEvents = $table->getNextEventsAfter(date('Y-m-d'));

		return array(
			'items' => $items,
			'nextEvents' => $nextEvents,
		);
	}

	protected function _getRandomItems($dataTypes, User_Row $user, Lib_Acl $acl, $amount = 3)
	{
		$items = array();

		$i = 0;
		while(($i < $amount) && (!empty($dataTypes))){
			$dataType = array_shift($dataTypes);
			$tableName = Data::mapDataType($dataType);
			$table = new $tableName();
			$randElements = $table->getRandom();
			$current = $randElements->current();
			if(empty($current)){
				continue;
			}
			if(!$current->isReadableBy($user, $acl)){
				continue;
			}
			$items[] = $current;
			$i++;
		}
		return $items;
	}

	protected function _getLatestItems($dataTypes, User_Row $user, Lib_Acl $acl, $amount = 4)
	{
		$items = array();

		$i = 0;
		while(($i < $amount) && (!empty($dataTypes))){
			$dataType = array_shift($dataTypes);
			$tableName = Data::mapDataType($dataType);
			$table = new $tableName();
			$latestElements = $table->getLatest();
			$current = $latestElements->current();
			if(empty($current)){
				continue;
			}
			if(!$current->isReadableBy($user, $acl)){
				continue;
			}

			$items[] = $current;
			$i++;
		}
		return $items;
	}
}
