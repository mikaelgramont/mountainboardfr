<?php
class Lib_View_Helper_MediaPhotoRotateLinks extends Zend_View_Helper_Abstract
{
    public function mediaPhotoRotateLinks(Media_Item_Row $photo)
    {
        $viewVars = $this->view->getVars();
        $baseUrl = $viewVars['baseUrl'].'/';
        $translator = Globals::getTranslate();

        $elements = array(
        	array(
        		'url' => Globals::getRouter()->assemble(array('id' => $photo->id, 'angle' => 90), 'rotatephoto', true),
        		'image' => $this->view->cdnHelper->url($baseUrl.IMAGES_PATH.'actions/rotate90ccw.png'),
        		'title' => 'photoRotate90'
        	),
        	array(
        		'url' => Globals::getRouter()->assemble(array('id' => $photo->id, 'angle' => 180), 'rotatephoto', true),
        		'image' => $this->view->cdnHelper->url($baseUrl.IMAGES_PATH.'actions/rotate180.png'),
        		'title' => 'photoRotate180'
        	),
        	array(
        		'url' => Globals::getRouter()->assemble(array('id' => $photo->id, 'angle' => 270), 'rotatephoto', true),
        		'image' => $this->view->cdnHelper->url($baseUrl.IMAGES_PATH.'actions/rotate90ccw.png'),
        		'title' => 'photoRotate270'
        	),

        );

        $content = "<ul class=\"photoRotationLinks\">".PHP_EOL;
        foreach($elements as $element){
            $title = ucfirst($translator->translate($element['title']));
            $content .= "<li><a title=\"$title\" href=\"{$element['url']}\">".PHP_EOL;
            $content .= "    <img src=\"{$element['image']}\" class=\"inset\" alt=\"$title\" />".PHP_EOL;
            $content .= "</a></li>".PHP_EOL;
        }
		$content .= "</ul>".PHP_EOL;

		return $content;
    }
}