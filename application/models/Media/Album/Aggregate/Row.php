<?php
class Media_Album_Aggregate_Row extends Media_Album_Row
{
	public $page;

	/**
	 * List of allowed album aggregation types
	 * Must not include 'rider', since that is taken care of
	 * separately
	 *
	 * @var array
	 */
	public static $allowedSimpleAggregations = array(
		'dpt',
		'spot',
		'trick',
		'author',
		'submitter'
	);

	/**
	 * Constructor
	 *
	 * @param array $config
	 * @param int $page
	 */
	public function __construct(array $config)
	{
		parent::__construct($config);
	}

	protected function _getItems()
	{
		$cache = $this->getCache();
		$cacheId = $this->getItemsCacheId();

		if(!$albumItems = $cache->load($cacheId)){

			$aggregationTable = new Media_Album_Aggregation();
		    $where = $aggregationTable->getAdapter()->quoteInto('albumId = ?', $this->id);
			$aggregationRow = $aggregationTable->fetchRow($where);
			if(empty($aggregationRow)){
				return;
			}

			if($aggregationRow->keyName == Media_Album_Aggregation::KEYNAME_USER){
				$rawItems = $this->_getRiderAggregationItems($aggregationRow->keyValue);
				$parentTable = new User();
			} else {
				$parentType = Data::mapDataType($aggregationRow->keyName);
				$parentTable = new $parentType();
				$rawItems = $this->_getSimpleAggregationItems($aggregationRow);
			}
			if(empty($rawItems)){
				return;
			}

			$this->_parentItem = $parentTable->find($aggregationRow->keyValue)->current();

			$albumItems = array();
			foreach($rawItems as $rawAlbumItem){
				$albumItems[] = Media_Item_Factory::buildItem($rawAlbumItem->id, $rawAlbumItem->mediaType);
			}

			$this->getTable()->saveDataInCache($cache, $albumItems, $cacheId);
		}

		return $albumItems;
	}

    /**
     * Clears all cache entries related to this object
     */
    public function clearCache()
    {
    	$cache = $this->getCache();
    	$cacheIds = array(
    		$this->_getCommentsCacheId(),
    		$this->getItemsCacheId(),
    		$this->_getTranslatedTextsCacheId(),
    	);

    	foreach($cacheIds as $cacheId){
    		$cache->remove($cacheId);
    	}
    }

	/**
	 * Aggregation via the intermediate media_riders_items table
	 * Here we return a list of media items that the given rider
	 * is tagged in.
	 * This information is held in a separate table, in order to
	 * be able to tag several riders on a single media item.
	 *
	 * @param int $riderId
	 * @return Zend_Db_Table_Rowset
	 */
	protected function _getRiderAggregationItems($riderId)
	{
		// Build the list of media where the rider $riderId appears
		$select = new Zend_Db_Select(Globals::getMainDatabase());
		$select->from(Constants_TableNames::MEDIAITEMRIDERS)
			   ->distinct()
			   ->columns('mediaId')
			   ->where('riderId = '.$riderId);
		$rowset = Globals::getMainDatabase()->query($select);
		if(empty($rowset)){
			return null;
		}

		$mediaIds = array();
		foreach($rowset as $row){
			$mediaIds[] = $row['mediaId'];
		}
		if(empty($mediaIds)){
			return array();
		}

		// Get the rowset of medias with the list we just built
		$itemsTable = new Media_Item();
		$where = 'id IN ('.implode(', ', $mediaIds).')';
		$rawItems = $itemsTable->fetchAll($where);
		return $rawItems;
	}

	/**
	 * Simple aggregation
	 * Here we return a rowset of media items which have
	 * an attribute that matches the aggregation.
	 * This attribute is held in the table itself.
	 *
	 * @param Media_Album_Aggregation_Row $aggregationRow
	 * @param Data_Row $parentTable
	 * @return Zend_Db_Table_Rowset
	 */
	protected function _getSimpleAggregationItems(Media_Album_Aggregation_Row $aggregationRow)
	{
		if(in_array($aggregationRow->keyName, self::$allowedSimpleAggregations)){
			$where = "`{$aggregationRow->keyName}` = {$aggregationRow->keyValue}";
		} else {
			$where = "`id` = {$aggregationRow->keyValue}";
		}

		$itemsTable = new Media_Item();
		$rawItems = $itemsTable->fetchAll($where);
		return $rawItems;
	}
}