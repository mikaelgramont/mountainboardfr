<?php
class Country extends Data
{
    const ITEM_TYPE = 'country';

    const FRANCE_ID = 1;
    
    protected $_name = Constants_TableNames::COUNTRY;

    protected $_itemType = 'country';

    protected $_rowClass = 'Country_Row';

	protected $_dependentTables = array('Dpt','Country');

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
	);

	public static function getLookupTable($cache)
	{
		$cacheId = 'countryLookup';
		if($l = $cache->load($cacheId)){
			return $l;
		}
		
		$table = new self();
		$results = $table->fetchAll();
		
		$l = array();
		foreach($results as $result){
			$l[strtolower($result->title)] = $result->id;
		}
		
		$cache->save($l, $cacheId);
		return $l;
	}

	/**
	 * When doing a reverse geocoding on a location,
	 * depending on the country, we may want different
	 * levels.
	 * Ex: 1st level for France is 'regions', but we use 'departements'.
	 * 
	 * @param string $countryName
	 */
	public static function getDptLevel($countryName)
	{
		$countryName = strtolower($countryName);
		switch($countryName){
			case 'france':
				$return = 'administrative_area_level_2';
				break;
			default:
				$return = 'administrative_area_level_1';
				break;				
		}
		
		return $return;
	}

	public static function getTextForPage($countryId)
	{
		if($countryId == self::FRANCE_ID){
			return 'franceDesc';
		}
		
		return 'countryTextDefault';
	}
}