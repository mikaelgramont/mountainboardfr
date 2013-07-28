<?php
class Lib_View_Helper_AlbumPreview extends Zend_View_Helper_Abstract
{
	public function albumPreview(Media_Album_Row $album, $limit = 4, $doRotations = false)
	{
		$medias = $this->view->albumContent($album, 1, $limit);
		
		$content  = '<ul class="albumPreview">'.PHP_EOL;
		$style = "";
		foreach($medias as $media){
			if($doRotations){
				$angle = rand(THUMBNAIL_ROTATION_MIN, THUMBNAIL_ROTATION_MAX);
				$style = " style=\"-webkit-transform: rotate({$angle}deg);-moz-transform: rotate({$angle}deg);transform: rotate({$angle}deg);\"";
			}
			$content .= "<li$style>".$this->view->mediaThumbnail($media).'</li>'.PHP_EOL;
		}
		$content .= '</ul>'.PHP_EOL;
		$linkInfo = array(
			'link' => $album->getLink(),
			'itemType' => $album->getItemType(),
			'name' => ucfirst(sprintf($this->view->translate('albumPreviewLink'), $album->getTitle())),
		);
		$content .= '<div class="actionLinkContainer albumPreviewLink">'.$this->view->itemLink($linkInfo).'</div>'.PHP_EOL;
		
		return $content;
	}
}