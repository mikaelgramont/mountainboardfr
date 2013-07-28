<?php
class Lib_View_Helper_HomePageSlides extends Zend_View_Helper_Abstract
{
	/**
	 * Generates the markup for the fetured content slides, usually for the home page.
	 *
	 * @return unknown
	 */
	public function homePageSlides()
	{
		$items = array();
		
		$testTable = new Test();
		$dossierTable = new Dossier();
		
		$test = $testTable->find(17)->current();
		if($test){
			$items[] = array(
				'image'=> IMAGES_PATH.'slides/mbs-f4.png',
				'thumbnail' => IMAGES_PATH.'slides/mbs-f4-small.png',
				'alt' => 'mbs f4',
				'linkCaption' => $test->getTitle(),
				'text' => $test->getDescription(),
				'link' => $test->getLink(),
			);
		}
				
		$dossier = $dossierTable->find(1)->current();
		if($dossier){
			$items[] = array(
				'image'=> IMAGES_PATH.'slides/powerslide.jpg',
				'thumbnail' => IMAGES_PATH.'slides/powerslide-small.jpg',
				'alt' => ucfirst($this->view->translate('slide_articlePowerSlide_alt')),
				'linkCaption' => ucfirst($this->view->translate('slide_articlePowerSlide_linkCaption')),
				'text' => ucfirst($this->view->translate('slide_articlePowerSlide_text')),
				'link' => $dossier->getLink(),
			);
		}
				
		$dossier = $dossierTable->find(7)->current();
		if($dossier){
			$items[] = array(
				'image'=> IMAGES_PATH.'slides/ramps.jpg',
				'thumbnail' => IMAGES_PATH.'slides/ramps-small.jpg',
				'alt' => ucfirst($this->view->translate('slide_articleRamps_alt')),
				'linkCaption' => ucfirst($this->view->translate('slide_articleRamps_linkCaption')),
				'text' => ucfirst($this->view->translate('slide_articleRamps_text')),
				'link' => $dossier->getLink(),
			);
		}
			
		$items[] = array(
			'image'=> IMAGES_PATH.'slides/forum.jpg',
			'thumbnail' => IMAGES_PATH.'slides/forum-small.jpg',
			'alt' => ucfirst($this->view->translate('slide_forums_alt')),
			'linkCaption' => ucfirst($this->view->translate('slide_forums_linkCaption')),
			'text' => ucfirst($this->view->translate('slide_forums_text')),
			'link' => '/forum',
		);
		
		shuffle($items);
				
		$content = '<div id="slider" >  
	<ul class="ui-tabs-nav">'.PHP_EOL;

		foreach($items as $index => $item){
			$content .= $this->_getListItem($index, $item['thumbnail'], $item['alt'], $item['text']) . PHP_EOL;
		}
		
		$content .= '	</ul>'.PHP_EOL;
		
		foreach($items as $index => $item){
			$content .= $this->_getContentItem($index, $item['image'], $item['alt'], $item['link'], $item['linkCaption'], $item['text']) . PHP_EOL;
		}		
		
		$content .= '</div>'.PHP_EOL;
    	return $content;
	}
	
	protected function _getListItem($index = 0, $thumbnail = null, $alt = '')
	{
		$classes = 'ui-tabs-nav-item';
		$classes .= (($index == 0) ? ' ui-tabs-selected' : '');
		
		$id = 'nav-fragment-'.$index;
		$href = '#fragment-'.$index;
		
		$image = (empty($thumbnail) ? '' : "<img src=\"$thumbnail\" alt=\"$alt\" />");
		
		$return = <<<ITEM
		<li class="$classes" id="$id"><a href="$href">$image</a></li>
ITEM;
		return $return;
	}
	
	protected function _getContentItem($index = 0, $image = null, $alt = '', $link='#', $linkCaption = '', $text = '')
	{
		$id = 'fragment-'.$index;
		$classes = 'ui-tabs-panel';
		$classes .= (($index !== 0) ? ' ui-tabs-hide' : '');
				
		$descriptionClass = "info";
		$readMore = ucfirst($this->view->translate('readMore')).'...';
		
		$return = <<<CONTENT
	<div id="$id" class="$classes">  
		<img src="$image" alt="$alt" />  
		<div class="$descriptionClass" >  
			<h2><a href="$link" >$linkCaption</a></h2>  
			<p>$text - <a href="$link" >$readMore</a></p>  
		</div>  
	</div>		
CONTENT;
		return $return;
	}
	
	public function getScripts()
	{
		$this->view->JQuery()->uiEnable()
							 ->addOnLoad($this->_getJavascript());		
	}
	
    /**
     * Return the javascript used to make the menu work
     *
     * @param string $menuId
     * @return string
     */
    protected function _getJavascript()
    {
        $js = <<<JS
	$("#slider").tabs({fx:{opacity: "toggle"}}).tabs("rotate", 5000, true);        

JS;
        return $js;
    }	
}