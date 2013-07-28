<?php
class Media_Album_Aggregate_Item_Row extends Media_Album_Aggregate_Row
{
	public $itemType;
	public $itemId;

	public function __construct(array $config)
	{
		if(!isset($config['itemType']) || empty($config['itemType'])){
			$this->itemType = 'albumId';
			if(array_key_exists('id', $config['data'])){
				$this->itemId = $config['data']['id'];	
			} else {
				$this->itemId = $this->id;
			}
			
		} else {
			$this->itemType = $config['itemType'];	
			if(!isset($config['itemId']) || empty($config['itemId'])){
				throw new Lib_Exception("No itemId given in config for instanciation of Media_Album_Aggregate_Item_Row");
			} else {
				$this->itemId = $config['itemId'];	
			}
		}

		if(isset($config['page'])){
			$this->page = $config['page'];
		} else {
			$this->page = 1;
		}

		parent::__construct($config, $this->page);
		
	}

	protected function _getItems()
	{
		$cache = $this->getCache();
		$cacheId = $this->getItemsCacheId();

		if(ALLOW_CACHE || !($albumItems = $cache->load($cacheId))){
			$sql = "
				SELECT i.id, i.mediaType
				FROM ".Constants_TableNames::ALBUM." a
				JOIN ".Constants_TableNames::AGGREGATION." ag ON ag.albumId = a.id
				JOIN ".Constants_TableNames::MEDIA." i ON i.$this->itemType = ag.keyValue
				WHERE ag.keyName = '$this->itemType' AND ag.keyValue = $this->itemId
				ORDER BY i.id DESC
			";

			$stmt = Globals::getMainDatabase()->query($sql);
			$albumItems = $stmt->fetchAll();
			if(ALLOW_CACHE){
				$this->getTable()->saveDataInCache($cache, $albumItems, $cacheId);
			}
		}

		return $albumItems;
	}
	
    /**
     * Refreshes properties from the database.
     *
     * @return void
     */
    protected function _refresh()
    {
        $where = $this->_getWhereQuery();
        $row = $this->_getTable()->fetchRowMod($where, null, $this->itemType, $this->itemId, $this->page);

        if (null === $row) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception('Cannot refresh row as parent is missing');
        }

        $this->_data = $row->toArray();
        $this->_cleanData = $this->_data;
        $this->_modifiedFields = array();
    }	
}
