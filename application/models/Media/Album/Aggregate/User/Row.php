<?php
class Media_Album_Aggregate_User_Row extends Media_Album_Aggregate_Row
{
	public $user;

	/**
	 * Constructor
	 *
	 * @param array $config
	 * @param int $page
	 */
	public function __construct(array $config)
	{
		parent::__construct($config);
		if(!isset($config['user']) || !($config['user'] instanceof User_Row)){
			throw new Lib_Exception("No User_Row given in config for instanciation of Media_Album_Aggregate_User_Row");
		} else {
			$this->user = $config['user'];
		}
		if(!isset($config['page'])){
			$this->page = $config['page'];
		} else {
			$this->page = 1;
		}
	}

	protected function _getItems()
	{
		$cache = $this->getCache();
		$cacheId = $this->getItemsCacheId();

		if(ALLOW_CACHE || !($albumItems = $cache->load($cacheId))){
			$userId = $this->user->getId();
			$sql = "
				SELECT i.id, i.mediaType
				FROM ".Constants_TableNames::ALBUM." a
				JOIN ".Constants_TableNames::AGGREGATION." ag ON ag.albumId = a.id
				JOIN ".Constants_TableNames::MEDIAITEMRIDERS." mir ON mir.riderId = ag.keyValue
				JOIN ".Constants_TableNames::MEDIA." i ON i.id = mir.mediaId
				WHERE ag.keyName = 'user' AND ag.keyValue = $userId
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
        $row = $this->_getTable()->fetchRowMod($where, null, $this->user, $this->page);

        if (null === $row) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception('Cannot refresh row as parent is missing');
        }

        $this->_data = $row->toArray();
        $this->_cleanData = $this->_data;
        $this->_modifiedFields = array();
    }
}
