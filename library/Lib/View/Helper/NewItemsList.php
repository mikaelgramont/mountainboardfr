<?php
class Lib_View_Helper_NewItemsList extends Zend_View_Helper_Abstract
{
    public function newItemsList($items, $isMetadata = false)
    {
        $dataType = $index = null;
        $content = '<dl class="newItems">'.PHP_EOL;
        foreach($items as $index => $item){
        	if($dataType != $item['parent']['dataType']){
                if($index > 0){
                    // Close previous ul
                    $content .= '       </ul>'.PHP_EOL;
                    $content .= '    </dd>'.PHP_EOL;
                }
        		
                // New type of parent
	            $dataType = $item['parent']['dataType'];
	            $class = " class=\"$dataType\"";
	           	$itemName = ucfirst($this->view->translate('itemPlur_' . $dataType));
	        	$content .= "	<dt$class>$itemName</dt>".PHP_EOL;
	        	$content .= '	<dd>'.PHP_EOL;
	        	$content .= '		<ul>'.PHP_EOL;
        	}        	
        	
        	$content .= '			<li>'.$this->_getItemContent($item, $isMetadata).'</li>'.PHP_EOL;
        }
        
        $content .= '	</ul>'.PHP_EOL;
        $content .= '</dl>'.PHP_EOL;
        return $content;
    }
    
    protected function _getItemContent($item, $isMetadata)
    {
    	$dataType = $item['parent']['dataType'];
       	$translationName = 'itemSing_' . $dataType;

        $itemName = ucfirst($this->view->translate($translationName));
        switch($dataType){
        	case Constants_DataTypes::FORUMTOPIC:
        		if(!$isMetadata){
        			foreach($item['children'] as $index => $child){
        				if($child['dataType'] == Constants_DataTypes::FORUMPOST){
        					// We don't want to count the original message as a reply
        					$item['children'][$index]['count']--;
        					// We don't want 'O replies' to be shown
        					if($item['children'][$index]['count'] <= 0){
        						unset($item['children'][$index]);
        					}
        				}
        			}
        		}
         		$parentTitle = ucfirst($item['parent']['object']->getTitle());
           		$forum = $item['parent']['object']->getForum();
       			$dataTypeContent = "<a class=\"{$dataType} dataLink\" href=\"".$item['parent']['object']->getLink()."\"> $parentTitle</a>";
           		$dataTypeContent .= " (<a class=\"forum dataLink\" href=\"".$forum->getLink()."\">".$forum->getTitle()."</a>)";
           		break;
           	case Constants_DataTypes::PHOTO:
           	case Constants_DataTypes::VIDEO:
           		$parentTitle = ucfirst($item['parent']['object']->getTitle());
           		$album = $item['parent']['object']->getAlbum();
       			$dataTypeContent = "<a class=\"{$dataType} dataLink\" href=\"".$item['parent']['object']->getLink()."\">$parentTitle</a>";
           		$dataTypeContent .= " (<a class=\"album dataLink\" href=\"".$album->getLink()."\">".$album->getTitle()."</a>)";
           		break;
           	case Constants_DataTypes::PRIVATEMESSAGE:
           		$parentTitle = ucfirst(sprintf($this->view->translate('messageFrom'), $item['parent']['object']->getSubmitter()->getTitle()));
           		$dataTypeContent = "<a class=\"{$dataType} dataLink\" href=\"".Globals::getRouter()->assemble(array(), 'privatemessageshome', true)."\">$parentTitle</a>";
           		break;
           	default:
           		$parentTitle = ucfirst($item['parent']['object']->getTitle());
       			$dataTypeContent = "<a class=\"{$dataType} dataLink\" href=\"".$item['parent']['object']->getLink()."\">$parentTitle</a>";
           		break;
        }

        $dataTypeMetaData = array();
        foreach($item['children'] as $childType){
          	if($isMetadata){
           		$itemName = $this->view->translate('newItem' . ($childType['count'] > 1 ? 'Plur' : 'Sing') . '_' .$childType['dataType']);
           	} else {
           		$itemName = $this->view->translate('item' . ($childType['count'] > 1 ? 'Plur' : 'Sing') . '_' .$childType['dataType']);
            }
          	$dataTypeMetaData[] = "<a class=\"{$childType['dataType']} dataLink\" href=\"{$childType['link']}\">{$childType['count']} $itemName</a>";
        }
        if(count($dataTypeMetaData)){
        	if($isMetadata){
            	$dataTypeContent = implode(', ', $dataTypeMetaData) . ' '.$this->view->translate('for').' '.$dataTypeContent;
        	} else {
        		$dataTypeContent .= ' + '.implode(', ', $dataTypeMetaData);
        	}
        }
        
    	return $dataTypeContent;
    }
}