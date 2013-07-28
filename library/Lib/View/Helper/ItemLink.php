<?php
class Lib_View_Helper_ItemLink extends Zend_View_Helper_Abstract
{
    /**
     * Return a link to an item, or a string of its name,
     * depending on input parameters
     *
     * @param array $linkInfo
     * @param mixed $class
     * @return string
     */
    public function itemLink($linkInfo, $staticClass = null, $rel = null, $title = null)
    {
    	$classes = array();
		if(!empty($staticClass)){
			$classes[] = $staticClass;
		}

    	if(!is_array($linkInfo)){
    		if(!($linkInfo instanceof Data_Row)){
    			throw new Lib_Exception("Cannot create an itemLink for objet of instance:" . get_class($linkInfo));
    		}
            // Not an array, but an item
            $link = $linkInfo->getLink();
            $name = $linkInfo->getTitle();
            $itemType = $linkInfo->getItemType();
        } else {
            $link = array_key_exists('link', $linkInfo) ? $linkInfo['link'] : '';
            $name = array_key_exists('name', $linkInfo) ? $linkInfo['name'] : '';
			$itemType = array_key_exists('itemType', $linkInfo) ? $linkInfo['itemType'] : '';
        }

		// We will not use the class corresponding to the item, if linkRight or linkLeft was provided
		if(!in_array($staticClass, array('linkRight','linkLeft'))){
           	$classes[] = $this->_getClassNameFromItem($itemType);
		}

        $name = ucfirst($name);

		if(!empty($classes)){
			$class = ' class="'.implode(' ', $classes).'"';
		} else {
			$class = '';
		}

        if(!empty($rel)){
            $rel = " rel=\"$rel\"";
        }

        if($title !== null){
        	$title = ' title="'.$title.'"';
        } else {
        	$title = ' title="'.ucfirst($this->view->translate('itemSing_'.$itemType)) . ' - ' . $this->view->escape($name).'"';
        }

        if(empty($link)){
        	$content = "<span $class$rel>$name</span>";
        } else {
        	$content = "<a$class$rel$title href=\"".$link.'">'.$name.'</a>';
        }
    	return $content;
    }

    protected function _getClassNameFromItem($itemType)
    {
    	switch($itemType){
    		case 'spot':
    		case 'album':
    		case 'photo':
    		case 'video':
    		case 'topic':
    		case 'forum':
    		case 'trick':
    		case 'news':
   			case 'dpt':
   			case 'comment':
    		case 'blog':
    		case 'blogpost':
    		case 'test':
    		case 'dossier':
   			case 'event':
    			$className = $itemType.' dataLink';
    			break;
    		case 'mediaalbum':
    			$className = 'album dataLink';
    			break;
    		case 'media':
    		case 'location':
    		default:
    			$className = null;
    			break;
    	}
    	return $className;
    }
}