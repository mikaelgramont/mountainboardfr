<?php
class Import extends Zend_Db_Table
{
	protected $_name = Constants_TableNames::IMPORT;

	public static function getOldUrls($oldServerName)
	{
		if(!ALLOW_CACHE){
			return array();
		}
		$cache = Globals::getAppCache();
		$id = 'urlConversion';

		$urlConversions = $cache->load($id, true);
		if($urlConversions !== false){
			return $urlConversions;
		}

		$db = Globals::getMainDatabase();
		$sql = "SELECT url, oldUrl FROM ".Constants_TableNames::IMPORT." ORDER BY oldUrl";
		$rowset = $db->query($sql)->fetchAll();

		$urlConversions = array();
		foreach($rowset as $row){
			if(empty($row['oldUrl'])){
				continue;
			}
        	$urlConversions[$oldServerName.$row['oldUrl']] = $row['url'];
		}

		$cache->save($urlConversions, $id);
		return $urlConversions;
	}
}