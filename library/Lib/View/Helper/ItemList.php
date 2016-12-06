<?php
class Lib_View_Helper_ItemList extends Zend_View_Helper_Abstract
{
    /**
     * Render a list of items (thumbnail, title, etc.)
     *
     * @param array $items
     * @return string
     */
    public function itemList($items, $userParams = array(), $showArchive = false)
    {
        $params = $this->_getDefaultParameters();
        $params = array_merge($params, $userParams);
        $id = $params['containerId'];

        if(count($items) == 0){
            return "<div id=\"$id\"></div>".PHP_EOL;
        }

        $classes = array();
        if($params['containerClass']){
        	$classes[] = "mediaBlock";
        	$classes[] = $params['containerClass'];
        }
        if($params['itemClass']){
        	$classes[] = $params['itemClass'];
        }
        $containerClasses = implode(' ', $classes);

        $content = "<ul id=\"$id\" class=\"$containerClasses\">".PHP_EOL;
        foreach($items as $item){
        	$content .= $this->_renderItem($item, $params);
        }

        if($showArchive){
			$content .= '<li class="archivesLink">'.PHP_EOL;
			$content .= $this->view->routeLink(
				'listnews',
				ucfirst($this->view->translate('indexMoreArticles'))).PHP_EOL;
			$content .= '</li>'.PHP_EOL;
        }
        $content .= '</ul>'.PHP_EOL;
        return $content;
    }
    
    protected function _renderItem($item, $params)
    {
    	$statusString = $editLink = $deleteLink = $href = '';
    	
    	if($item->isEditableBy($this->view->user, $this->view->acl)){
    		$statusString = $this->view->itemStatus($item, true);
    		$editLink = $this->view->editLink($item);
    	}
    	if($item->isDeletableBy($this->view->user, $this->view->acl)){
    		$deleteLink = $this->view->deleteLink($item);
    	}
    	
    	$link = $item->getLink();
    	$readMoreTitle = ucwords($item->getTitle());
    	$title = ucfirst($this->view->translate(
    		'itemSing_'.$item->getItemType())) . ' - ' . $readMoreTitle;
    	
    	$classes = array();
    	$classes[] = 'mainLink';
    	if($params['addDataLinkClass']){
    		$classes[] = $item->getItemType().'Link';
    		$classes[] = 'dataLink';
    	}
    	$linkClass = implode(' ', $classes);
    	
    	$itemClass = $params['itemClass'];
    	$content = "<li class=\"$itemClass\">".PHP_EOL;
    	if($params['useAnchor']){
    		$name = $params['anchorPrefix'].$item->id;
    		$content .= "<a name=\"$name\"></a>".PHP_EOL;
    	}
    	if(method_exists($item, 'getThumbnail')){
    		$thumbnailSrc = $this->view->cdnHelper->url('/'.$item->getThumbnail());
    		$content .= "	<a href=\"$link\" class=\"img\" aria-hidden=\"true\">".PHP_EOL;
    		$content .= "		<img src=\"".$this->view->baseUrl.$thumbnailSrc."\" alt=\"\"/>".PHP_EOL;
    		$content .= "	</a>".PHP_EOL;
    	}
    	$content .= '	<div class="bd">'.PHP_EOL;
    	$content .= '		<h1>'.PHP_EOL;
    	if($params['link']){
    		// Make the header a link.
    		$content .= "  	<a class=\"$linkClass\" title=\"$title\" href='".$link."'>".ucfirst($readMoreTitle)."</a>".PHP_EOL;
    	} else {
    		// The header is just text.
    		$content .= "   ".ucfirst($readMoreTitle).PHP_EOL;
    	}
    	$content .= "</h1>".PHP_EOL;
    	$content .= $statusString.$editLink.$deleteLink.PHP_EOL;
    	
    	if($params['showPostInfo']){
    		$content .= ' '.$this->_getPostInformation($item, $params['postInfoClass']).PHP_EOL;
    	}
    	if($params['showDate']){
    		$content .= ' '.$this->_getDate($item, $params).PHP_EOL;
    	}
    	
    	if($params['editionInfo']){
    		$content.='<br/>';
    		$content .= '   '.$this->view->editionInformation($item, $params['editionInfoClass']).PHP_EOL;
    	}
    	
    	if($item instanceof Event_Row){
    		$renderer = new Lib_View_Helper_RenderData_Event($this->view);
    		$content .= $renderer->renderDates($item);
    	}
    	
    	$description = $item->getDescription();
    	if($params['striptags']){
    		$description = '<span class="description">'.strip_tags($description).'</span>'.PHP_EOL;
    	}
    	
    	if(!($item instanceof Trick_Row) && !($item instanceof Spot_Row)){
    		if($params['shortenDescription']){
    			$length = Utils::strlen($description);
    			if($length > $params['maxDescriptionLength']){
    				$description = Utils::substr($description, $params['maxDescriptionLength']) . '... [<a href="'.$link.'">'.ucfirst($this->view->translate('readMore')).'</a>]';
    			}
    		}
    		$content .= '   '.$description.PHP_EOL;
    	}
    	if($item instanceof Spot_Row){
    		$location = $item->getLocation();
    		if($location && $dpt = $location->getDpt()){
    			$content .= ' - '.$this->view->itemLink($dpt);
    		}
    	}
    	
    	
    	if($params['readMore']){
    		$content .= "<a href=\"$link\" title=\"$readMoreTitle\" class=\"readMoreLink\">".ucfirst($this->view->translate('readMore'))."...</a>".PHP_EOL;
    	}
    	$content .= "</div>".PHP_EOL;
    	$content .= "</li>".PHP_EOL;
    	return $content;
    }

    /**
     * Returns a list of default parameters used for rendering
     * (classes, id's)
     *
     * @return array
     */
    protected function _getDefaultParameters()
    {
        $params = array(
            'link' => true,
            'editionInfo' => false,

            'containerId' => 'items',
            'containerClass' => '',

            'itemClass' => '',

            'addDataLinkClass' => false,
            'showPostInfo' => false,
            'showDate' => false,
        	'postInfoClass' => '',
            'editionInfoClass' => '',

            'useAnchor' => false,
            'anchorPrefix' => '',

        	'shortenDescription' => false,
        	'maxDescriptionLength' => 0,
        	'striptags' => false,

        	'readMore' => false,
        );
        return $params;
    }

    /**
     * Returns information about who submitted this item
     *
     * @param Data_Row $item
     * @param string $postInfoClass
     * @return string
     */
    protected function _getPostInformation(Data_Row $item, $postInfoClass = null)
    {
        $info = $this->view->renderDataInformation($item, $postInfoClass, 'span');
        $content = "       $info".PHP_EOL;
        return $content;
    }

    /**
     * Returns the date this was submitted
     *
     * @param Data_Row $item
     * @param string $postInfoClass
     * @return string
     */
    protected function _getDate(Data_Row $item, $params)
    {
    	$info = ' <span class="'.$params['postInfoClass'].'">('. $item->getDate(false);

    	if(isset($params['author']) && $author = $item->getAuthor()){
        	$info .= ', '.$this->view->translate('by').' '.$this->view->userLink($author).PHP_EOL;
		}

    	$info .= ')</span>'.PHP_EOL;
        $content = "       $info".PHP_EOL;
        return $content;
    }
}