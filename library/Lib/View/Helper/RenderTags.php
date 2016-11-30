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
		if(empty($tags) || !is_array($tags)){
			return '';
		}
		
    	$content = '<div class="tags">Tags: <ul>';
    	foreach ($tags as $tag) {
    		$content .= "<li>" . $this->view->routeLink(
    			'search', $tag, array('searchterms' => $tag)). "</li> ";
    	}
    	$content .= '</ul></div>'.PHP_EOL;
        return $content;
    }
}
