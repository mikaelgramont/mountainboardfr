<?php
class Lib_AssetCache
{
	const CACHE_ID = 'assetVersionning';
	
	public static function getLookupTable()
	{
		$cache = Globals::getGlobalCache();
		if($lookupTable = $cache->load(Lib_AssetCache::CACHE_ID)){
			return $lookupTable;
		}
		
		$lookupTable = self::buildLookupTable();
		return $lookupTable;
	}
	
	public static function buildLookupTable()
	{
		$cache = Globals::getGlobalCache();
		
		$assetVersions = array();
		@include('../data/lookupTable.php');
		$lookupTable = $assetVersions;
		
		$cache->save($lookupTable, Lib_AssetCache::CACHE_ID);
		
		return $lookupTable;
	}
}