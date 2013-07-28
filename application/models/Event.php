<?php
class Event extends Data
{
    const ITEM_TYPE = 'event';

 	const EVENT_TYPE_COMPETITION = 'competition';
 	const EVENT_TYPE_SESSION = 'session';
 	const EVENT_TYPE_DEMO = 'demo';

    protected $_itemType = 'event';

    protected $_name = Constants_TableNames::EVENT;

    protected $_rowClass = 'Event_Row';

    protected $_linkString = 'evenement';

    protected $_referenceMap    = array(
        'LastEditor' => array(
            'columns'           => 'last_editor',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'Submitter' => array(
            'columns'           => 'submitter',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'Author' => array(
            'columns'           => 'author',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'Spot' => array(
            'columns'           => 'spot',
            'refTableClass'     => 'Spot',
            'refColumns'        => 'id'
        ),
    );

	public function getNextEventsAfter($date)
	{
		$cacheId = $this->getNextEventsAfterCacheId($date);
		$cache = $this->getCache();
		if(ALLOW_CACHE){
			$results = $cache->load($cacheId);
			if($results !== false){
				return $results;
			}
    	}

		$sql = $this->getAdapter()->quoteInto("SELECT id FROM ".Constants_TableNames::EVENT." WHERE startDate >= ? ORDER BY startDate ASC LIMIT 3", $date);
		$stmt = $this->getAdapter()->query($sql);
		if(empty($stmt)){
			return null;
		}
		$ids = $stmt->fetchAll();
		if(empty($ids)){
			return null;
		}
		$string = array();
		foreach($ids as $id){
			$string[] = $id['id'];
		}

		$results = $this->fetchAll("id IN (".implode(', ', $string).")" ,"startDate ASC");
		if(empty($results)){
			$results = null;
		}

		$this->saveDataInCache($cache, $results, $cacheId);

		return $results;
	}

	public function getNextEventsAfterCacheId($date)
	{
		$cacheId = 'nextEventsAfter'.str_replace('-', '', $date);
		return $cacheId;
	}
}