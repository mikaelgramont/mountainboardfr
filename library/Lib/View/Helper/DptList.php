<?php
class Lib_View_Helper_DptList extends Zend_View_Helper_Abstract
{
    /**
     * Render a list of items (thumbnail, title, etc.)
     *
     * @param array $items
     * @return string
     */
    public function dptList($items, $elementId = 'dptList', $elementClass = '', $itemClass = '')
    {
        if(!count($items)){
            // No items: nothing to be rendered
            $content = "<div id='$elementId'>".PHP_EOL;
            $content .= "</div>".PHP_EOL;
            return $content;
        }

        if($elementClass){
            $elementClass = " class='$elementClass'";
        }
        if($itemClass){
            $itemClass = " class='$itemClass'";
        }

        $content = "<ul id='{$elementId}'$elementClass>".PHP_EOL;
        foreach($items as $index => $item){
            $content .= "<li$itemClass>".PHP_EOL;
            $content .= "   <a href='".$item->getLink()."'>[$item->id] ".$item->getTitle()."</a>".PHP_EOL;
            $content .= "</li>".PHP_EOL;
        }
        $content .= '</ul>'.PHP_EOL;
        return $content;
    }
}