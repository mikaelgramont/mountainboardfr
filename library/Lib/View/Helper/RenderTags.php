<?php
class Lib_View_Helper_RenderTags extends Zend_View_Helper_Abstract
{
    /**
     * Render a document
     *
     * @param Document_Row $document
     * @return string
     */
    public function renderTags($tags)
    {
		if(empty($tags)){
        	return '';
        }
		if(!is_array($tags)){
			return '';
		}        
    	$content = '<p class="tags">Tags: '.implode(' ', $tags).'</p>'.PHP_EOL;
        return $content;
    }
}
