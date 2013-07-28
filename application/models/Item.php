<?php
class Item extends Cache_Object
{
    protected $_name = Constants_TableNames::ITEM;

    protected $_rowClass = 'Item_Row';

    /**
     * Return all items posted since the specified date
     *
     * @param string $date
     * @param boolean $orderByType
     * @param string $orderByTypeDir
     * @param string $orderByDateDir
     * @param boolean $seeInvalidItems
     * @return Zend_Db_Table_Rowset
     */
    public static function getAllItemsPostedSince($from, $until, User_Row $user, Lib_Acl $acl, $maxItems)
    {
    	$db = Globals::getMainDatabase();
        $userId = $user->getId();
        $table = Constants_TableNames::ITEM;
        $silent = Item_Row::NOTIFICATION_SILENT;

        $parentValidCondition = $childValidCondition = '';
        if(!$acl->isAllowed($user, Lib_Acl::PUBLIC_EDIT_RESOURCE)){
        	$parentValidCondition = " AND parent.status = '".Data::VALID."'";
        	$childValidCondition = " AND child.status = '".Data::VALID."'";
        }

        if(empty($maxItems)){
        	$limit = "LIMIT ".MAX_NOTIFICATION_ITEMS_USERS;
        } else {
        	$limit = "LIMIT ". $maxItems;
        }

        $from = $db->quote($from);
        $parentUntil = $childUntil = '';
        if(!empty($until)){
			$until = $db->quote($until);
        	$childUntil = ' AND child.date < '.$until;
        	$parentUntil = ' AND parent.date < '.$until;
        }

        $medium = User_Notification::MEDIUM_HOMEPAGE;
        $notifTable = Constants_TableNames::USER_NOTIFICATIONS;

        $newElementsAndMetadataSql =
"SELECT
parent.itemId AS parentItemId, parent.itemType AS parentItemType,
child.itemType AS childItemType, COUNT(child.itemType) AS childItemCount, child.itemId AS childItemId,
parent.date AS parentItemDate, parent.status AS parentItemStatus,
child.date AS childItemDate,child.status AS childItemStatus
FROM $table parent
JOIN $notifTable n ON (n.itemType = parent.itemType AND n.notify = ".User_Notification::NOTIFY.")
LEFT JOIN $table child
ON (child.parentItemId = parent.id AND child.date > $from$childUntil$childValidCondition AND child.submitter <> $userId AND child.notification <> '$silent')
WHERE n.medium = '$medium' AND n.userId = $userId AND parent.date > $from$parentUntil$parentValidCondition
AND parent.parentItemId IS NULL AND (parent.submitter <> $userId OR child.submitter <> $userId) AND parent.notification <> '$silent'
GROUP BY parentItemId, parentItemType, child.parentItemId, childItemType
ORDER BY parentItemType ASC, parentItemId DESC
$limit;";

        $oldElementsAndNewMetadataSql =
"SELECT
parent.itemId AS parentItemId,parent.itemType AS parentItemType,
child.itemType AS childItemType, COUNT(child.itemType) AS childItemCount, child.itemId AS childItemId,
parent.date AS parentItemDate, parent.status AS parentItemStatus,
child.date AS childItemDate,child.status AS childItemStatus
FROM $table parent
JOIN $notifTable n ON (n.itemType = parent.itemType AND n.notify = ".User_Notification::NOTIFY.")
JOIN $table child ON (child.parentItemId = parent.id)
WHERE n.medium = '$medium' AND n.userId = $userId
AND child.date > $from AND parent.date < $from$childValidCondition$parentValidCondition
AND child.submitter <> $userId AND child.notification <> '$silent'
GROUP BY parentItemId, parentItemType, child.itemType
ORDER BY parentItemType ASC, parentItemId DESC
$limit;";

		$newElementsAndMetadata = self::_getParentAndChildItems($db->fetchAll($newElementsAndMetadataSql), $user, $acl);
		$oldElementsAndNewMetadata = self::_getParentAndChildItems($db->fetchAll($oldElementsAndNewMetadataSql), $user, $acl);

        return array(
            'newElementsAndMetadata' => $newElementsAndMetadata,
            'oldElementsAndNewMetadata' => $oldElementsAndNewMetadata
        );
    }

    public static function getNewItemsCacheId($userId, $interval, $maxItems)
    {
		$cacheId = 'newItemsForUser'.$userId.'Interval'.ucfirst($interval);
		if($maxItems){
			$cacheId .= 'Max'.$maxItems;
		}
    	return $cacheId;
    }

    protected static function _getParentAndChildItems($rowSet, User_Row $user, Lib_Acl $acl)
    {
        $parentId = null;
        $parentType = null;
        $items = array();

        $parentIndex = -1;
        foreach($rowSet as $row){
            if($parentId != $row['parentItemId'] || $parentType != $row['parentItemType']){
                // New parent Object
                $parentType = $row['parentItemType'];
                $parentId = $row['parentItemId'];
                $parent = Data::factory($parentId, $parentType);
				if(!$parent){
					$message = "Fetching new items - Parent object of itemType '$parentType' and itemId '$parentId' cannot be instanciated. It does not seem to exist";
					Globals::getLogger()->warning($message, Zend_Log::INFO);
					continue;
				}

				if(!$parent->isReadableBy($user, $acl)){
					continue;
				}

				$parentIndex++;
                $items[$parentIndex] = array(
                    'parent' => array(
						'object' => $parent,
						'dataType' => $parentType
					),
					'children' => array()
				);
            }

            // Children
            if($row['childItemCount'] > 0){
                $child = Data::factory($row['childItemId'], $row['childItemType']);
                if(!$child){
					$message = "Fetching new items - Child object of itemType '$parentType' and itemId '$parentId' cannot be instanciated. It does not seem to exist";
					Globals::getLogger()->warning($message, Zend_Log::INFO);
					continue;
                }

                if(!$child->isReadableBy($user, $acl)){
					continue;
				}

                $link = $child->getLink();
                $items[$parentIndex]['children'][] = array(
                    'count' => $row['childItemCount'],
                    'dataType' => $row['childItemType'],
                    'link' => $link
                );
            }
        }

        return $items;
    }

    /**
     * Return items of a specific type posted since the specified date
     *
     * @param string $date
     * @param string $itemType
     * @param string $orderByDateDir
     * @param boolean $seeInvalidItems
     * @return Zend_Db_Table_Rowset
     */
    public static function getItemsPostedSince($date, $itemType, $orderByDateDir = null, $seeInvalidItems = false)
    {
        if($orderByDateDir != 'ASC'){
            $orderByDateDir = 'DESC';
        }
        $orderBy = array("date $orderByDateDir");

        $itemsTable = new self();
        $where = "date > '$date' AND itemType = '$itemType''";
        if(!$seeInvalidItems){
            $where .= " AND status = '".Data::VALID."'";
        }
        $items = $itemsTable->fetchAll($where, $orderBy);
        return $items;
    }

    public static function wakeupItems(array $items)
    {
    	foreach($items as &$firstLevel){
    		foreach($firstLevel as &$row){
    			$parentTable = Data::mapDataType($row['parent']['dataType']);
    			$row['parent']['object']->setTable(new $parentTable);
    		}
    	}

    	return $items;
    }

    /**
     * Return the list of items that are displayed on the front page
     *
     * @param int $page
     */
    public function getArticles($page = 1, $itemType = null, $seeInvalidItems = false)
    {
        $cacheId = $this->getArticlesCacheId($page, $itemType);
		$cache = Globals::getGlobalCache();
		if(ALLOW_CACHE && !$seeInvalidItems){
			$results = $cache->load($cacheId);

			if($results !== false){
				return $results;
			}
    	}


        $itemTypeList = empty($itemType) ? $itemTypeList = implode("', '", Article::$articleClasses) : $itemType;
        $where = "itemType IN ('$itemTypeList') AND notification = '".Item_Row::NOTIFICATION_ANNOUNCE."'";
        if(!$seeInvalidItems){
            $where .= " AND status = '".Data::VALID."'";
        }

        $count = FRONTPAGE_ITEMS_PER_PAGE;
        // Manage the offset of 1
        $page--;
        if($page < 0){
            $page = 0;
        }
        $offset = $count * $page;

        $items = $this->fetchAll($where, 'date DESC', $count, $offset);

        $results = self::_arrayMembersToItemObjects($items);
        if(ALLOW_CACHE && !$seeInvalidItems){
        	$this->saveDataInCache($cache, $results, $cacheId);
        }

        return $results;
    }

    protected static function _arrayMembersToItemObjects($items)
    {
        $objects = array();
        foreach($items as $item){
            if(is_array($item)){
                $objects[] = Data::factory($item['parentItemId'], $item['parentItemType']);
            } else {
                $objects[] = Data::factory($item->itemId, $item->itemType);
            }
        }

        return $objects;
    }

	/**
	 * Takes an array of new elements and metadata, and filters those
	 * which are not marked as interesting in the user notification
	 * preferences, depending on "where" we are going to display the
	 * elements.
	 *
	 * @param array $items
	 * @param string $notificationType
	 * @param User_Row $user
	 * @return array
	 */
    public static function filterOutItems($allNewItems, $medium, $user)
	{
    	$items = array(
    		'newElementsAndMetadata' => array(),
    		'oldElementsAndNewMetadata' => array()
    	);
        $applicableNotifications = $user->getApplicableNotifications($medium);

        // Filter out new items of dataTypes that must not be shown
        foreach($allNewItems['newElementsAndMetadata'] as $newItem){
        	if(in_array($newItem['parent']['dataType'], $applicableNotifications)){
        		$items['newElementsAndMetadata'][] = $newItem;
        	}
        }

        // Filter out new metadata (of old items) of dataTypes that must not be shown
        foreach($allNewItems['oldElementsAndNewMetadata'] as $oldElementNewMetadata){
        	$toDelete = array(); // keys of the elements to be hidden

        	foreach($oldElementNewMetadata['children'] as $index => $child){
        		if(!in_array($child['dataType'], $applicableNotifications)){
					// Mark this child key as 'to be hidden' since it is not of an interesting dataType
        			$toDelete[] = $index;
        		}
        	}

        	foreach($toDelete as $key){
        		// Actually perform the deletion
        		unset($oldElementNewMetadata['children'][$key]);
        	}

			// Only report the element if it has new metadata to be shown
        	if(!empty($oldElementNewMetadata['children'])){
        		$items['oldElementsAndNewMetadata'][] = $oldElementNewMetadata;
        	}
        }

        return $items;
	}

	public static function getItemsInBounds($bounds, $filter = null)
	{
		$return = array();
		$db = Globals::getMainDatabase();

		$sql = "SELECT * FROM locations l
		where latitude between {$bounds[0]} and {$bounds[2]}
		and longitude between {$bounds[1]} and {$bounds[3]}
		and itemType not in('dpt')";

		$results = $db->fetchAll($sql);
    	foreach($results as $row){
    		if(is_array($filter) && !in_array($row['itemType'], $filter)){
    			continue;
    		}

   			$item = Data::factory($row['itemId'], $row['itemType']);
   			if(empty($item)){
   				continue;
   			}
   			$return[] = $item;
    	}
    	return $return;
	}

	public static function getItemsInBoundsAsJson(Zend_View $view, $bounds, $filter = null, $loggedIn = false)
	{
		$return = array();
		$items = self::getItemsInBounds($bounds, $filter);
	   	if(empty($items)){
   			return Zend_Json_Encoder::encode($return);
   		}

   		$itemTypes = array();

		foreach($items as $item){
			$location = $item->getLocation();
			$itemType = $item->getItemType();
			$id = $item->getId();

			$singularDisplayType = ucfirst($view->translate('itemSing_'.$itemType));
			if(isset($itemTypes[$itemType])){
				$displayType = ucfirst($view->translate('itemPlur_'.$itemType));
			} else {
				$displayType = $singularDisplayType;
				$itemTypes[$itemType] = true;
			}

			list($info, $link, $title) = Lib_View_Helper_RenderData::getItemInfoForDisplay($view, $item, $loggedIn);

			$return[] = array(
				'displayType' => $displayType,
				'itemType' => $itemType,
				'singularDisplayType' => $singularDisplayType,
				'link' => $link,
				'title' => $title,
				'id' => $id,
				'info' => $info,
				'itemTypeId' => $itemType.$id,
				'position' => array(
					$location->latitude,
					$location->longitude
				)
			);
		}

		$return = Zend_Json_Encoder::encode($return);
		return $return;
	}

	public function getLastPostedItemBy(User_Row $user, $itemType)
	{
		$itemRow = $this->fetchRow($this->_db->quoteInto("submitter = ?", $user->getId()), 'date DESC');
		return $itemRow;
	}

	public function getArticlesCacheId($page = 1, $itemType = null)
	{
		$return = 'articles_page'.$page;
		if($itemType){
			$return .= '_'.$itemType;
		}

		return $return;
	}

}