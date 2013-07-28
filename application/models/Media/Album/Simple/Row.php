<?php
class Media_Album_Simple_Row extends Media_Album_Row
{
	public $page;

	public function __construct(array $config = array(), $page = 1)
	{
		parent::__construct($config);
		$this->page = $page;
	}

	protected function _getItems()
	{
		$cache = $this->getCache();
		$cacheId = $this->getItemsCacheId();

		if(ALLOW_CACHE || !($albumItems = $cache->load($cacheId))){

			$db = Globals::getMainDatabase();

			$sql = "
				SELECT m.id, m.mediaType
				FROM ".Constants_TableNames::MEDIA." m
				WHERE m.albumId = $this->id
				ORDER BY m.id DESC
			";
			$albumItems = $db->query($sql)->fetchAll();
			if(ALLOW_CACHE){
				$this->getTable()->saveDataInCache($cache, $albumItems, $cacheId);
			}
		}

		return $albumItems;
	}

	public function getCategory()
	{
    	switch($this->id){
    		case Media_Album_PhotoMain::ID:
    		case Media_Album_VideoMain::ID:
    		default:
    		    return Category::COMMUNITY;
    			break;
    		case Media_Album_Portfolio::ID:
    		    return Category::START;
    			break;
    	}
	}

	public function getSubCategory()
	{
    	switch($this->id){
    		case Media_Album_PhotoMain::ID:
    		    return SubCategory::PHOTOS;
    			break;
    		case Media_Album_VideoMain::ID:
    		    return SubCategory::VIDEOS;
    			break;
    		case Media_Album_Portfolio::ID:
    		    return SubCategory::PORTFOLIO;
    			break;
    		default:
    			return SubCategory::NONE;
    	}
	}
}
