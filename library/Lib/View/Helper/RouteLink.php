<?php
/**
 * RouteLink view helper
 */
class Lib_View_Helper_RouteLink extends Zend_View_Helper_Abstract
{
    /**
     * Build and return a link to a route, with the given title
     * Will throw Zend_Controller_Router_Exception if $route does not exist
     * @param string $route
     * @param string $title
     * @param array $params
     * @throws Lib_Exception_Translate
     * @return string
     */
    public function routeLink($route, $title = null, $params = array(), $class="", $id="", $linkTitle="")
    {
        $translator = Globals::getTranslate();
        if($title === null){
            if(!$translator->isTranslated($route)){
                throw new Lib_Exception("Translation for $route is missing");
            }
            $title = ucfirst($translator->_($route));
        }

        $url = Globals::getRouter()->assemble($params, $route, true);
        if(!empty($class)){
        	$class= " class=\"$class\"";
        }
        if(!empty($id)){
        	$id= " id=\"$id\"";
        }
        if(!empty($linkTitle)){
        	$linkTitle= " title=\"$linkTitle\"";
        }
        $link = $this->link($url, $title, $class, $id, $linkTitle);

        return $link;
    }

    public function link($url, $title, $class, $id, $linkTitle="")
    {
    	$link = "<a $class $id $linkTitle href=\"$url\">$title</a>";
    	return $link;
    }
}