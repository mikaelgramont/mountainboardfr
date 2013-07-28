<?php
class Lib_View_Helper_AlbumContent extends Zend_View_Helper_Abstract
{
	public function albumContent(Media_Album_Row $album, $page, $limit = null)
	{
		if($limit === null){
			$limit = $album->getAmountPerPage();
		}
		$rawMediaItems = $album->getItemSet();
		$medias = Lib_PaginatorFactory::getPaginator($rawMediaItems, $page, $limit);
		return $medias;
	}
}