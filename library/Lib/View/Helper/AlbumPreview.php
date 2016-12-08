<?php
class Lib_View_Helper_AlbumPreview extends Zend_View_Helper_Abstract
{
	public function albumPreview(Media_Album_Row $album, $limit = 4)
	{
		$medias = $this->view->albumContent($album, 1, $limit);
    	$content = '<ul class="albumList albumPreview">'.PHP_EOL;
    	foreach($medias as $media){
    		$typeClass = $media['mediaType'] == Media_Item::TYPE_VIDEO ? 'video' : 'photo';
    		$content .= "<li class=\"media $typeClass\">".PHP_EOL;
    		$content .= $this->view->mediaThumbnail($media, null, null, true);		
    		$content .= '</li>'.PHP_EOL;
    	}
    	$content .= '</ul>'.PHP_EOL;
    		
		$linkInfo = array(
			'link' => $album->getLink(),
			'itemType' => $album->getItemType(),
			'name' => ucfirst(sprintf($this->view->translate(
			        'albumPreviewLink'), $album->getTitle())),
		);
		$content .= '<div class="albumPreviewLink">'.PHP_EOL;
		$content .= ' <div class="actionLinkContainer">'.PHP_EOL;
		$content .= $this->view->itemLink($linkInfo).PHP_EOL;
		$content .= ' </div>'.PHP_EOL;
		$content .= '</div>'.PHP_EOL;
		
		return $content;
	}
}