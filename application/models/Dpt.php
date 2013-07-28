<?php
class Dpt extends Data
{
    const ITEM_TYPE = 'dpt';

    const LAST_FRENCH_DPT_ID = 99;
    
    protected $_name = Constants_TableNames::DPT;

    protected $_itemType = 'dpt';

    protected $_rowClass = 'Dpt_Row';

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
        'Country' => array(
            'columns'           => 'country',
            'refTableClass'     => 'Country',
            'refColumns'        => 'id'
        ),
    );

	public static function getLookupTable($cache)
	{
		$cacheId = 'dptLookup';
		if($l = $cache->load($cacheId)){
			return $l;
		}
		
		$table = new self();
		$results = $table->fetchAll();
		
		$l = array();
		foreach($results as $result){
			$key = Utils::cleanStringForUrl($result->title) .'-'.$result->country;
			$l[$key] = $result->id;
		}
		
		$cache->save($l, $cacheId);
		return $l;
	}
}