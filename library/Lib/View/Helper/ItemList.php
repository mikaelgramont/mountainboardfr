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

        if(!count($items)){
            // No items: nothing to be rendered
            $content = "<div id='{$params['containerId']}'>".PHP_EOL;
            $content .= "</div>".PHP_EOL;
            return $content;
        }

        if($params['containerClass']){
            $params['containerClass'] = " class='mediaBlock {$params['containerClass']}'";
        }
        if($params['itemClass']){
            $params['itemClass'] = " class='{$params['itemClass']}'";
        }


        $content = "<ul id='{$params['containerId']}'{$params['containerClass']}>".PHP_EOL;
        foreach($items as $item){
            $statusString = $editLink = $deleteLink = '';

            if($item->isEditableBy($this->view->user, $this->view->acl)){
                $statusString = $this->view->itemStatus($item, true);
                $editLink = $this->view->editLink($item);
            }
            if($item->isDeletableBy($this->view->user, $this->view->acl)){
                $deleteLink = $this->view->deleteLink($item);
            }

            if($params['useAnchor']){
                $href = "<a name=\"{$params['anchorPrefix']}{$item->id}\"></a>";
            } else {
                $href = "";
            }

            $link = $item->getLink();
            $readMoreTitle = ucwords($item->getTitle());
            $title = ' title="'.ucfirst($this->view->translate('itemSing_'.$item->getItemType())) . ' - ' . $readMoreTitle.'"';

            $classes = array();
           	$classes[] = 'mainLink';
            if($params['addDataLinkClass']){
            	$classes[] = $item->getItemType();
            	$classes[] = 'dataLink';
            }
            $linkClass = ' class="'.implode(' ', $classes).'"';

            $content .= "<li{$params['itemClass']}>$href".PHP_EOL;
            if(method_exists($item, 'getThumbnail')){
            	$src = $this->view->cdnHelper->url('/'.$item->getThumbnail());
            	$content .= "	<div class=\"img\"><img src=\"".$this->view->baseUrl.$src."\" alt=\"\"/></div>".PHP_EOL;
            }
            $content .= "	<div class=\"bd\">".PHP_EOL.'<h1>'.PHP_EOL;
            if($params['link']){
                $content .= "  	<a$linkClass$title href='".$link."'>".ucfirst($readMoreTitle)."</a>$statusString$editLink$deleteLink".PHP_EOL;
            } else {
                $content .= "   ".ucfirst($readMoreTitle)."$statusString$editLink$deleteLink".PHP_EOL;
            }

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
            	$description = '<span class="description">'.strip_tags($description).'</span>';
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



        	$content .= "</h1>".PHP_EOL;

            if($params['readMore']){
            	$content .= "<a href=\"$link\" title=\"$readMoreTitle\" class=\"readMoreLink\">".ucfirst($this->view->translate('readMore'))."...</a>".PHP_EOL;
            }
            $content .= "</div>".PHP_EOL;
            $content .= "</li>".PHP_EOL;
        }


        if($showArchive){
			$content .= '<li class="archivesLink">'. $this->view->routeLink('listnews', ucfirst($this->view->translate('indexMoreArticles'))).'</li>'.PHP_EOL;
        }
        $content .= '</ul>'.PHP_EOL;
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