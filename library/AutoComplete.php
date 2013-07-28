<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category    ZendX
 * @package     ZendX_JQuery
 * @subpackage  View
 * @copyright   Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id: AutoComplete.php 14483 2009-03-25 17:48:17Z beberlei $
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once "ZendX/JQuery/View/Helper/UiWidget.php";

/**
 * jQuery Autocomplete View Helper
 *
 * @uses 	   Zend_Json, Zend_View_Helper_FormText
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
class ZendX_JQuery_View_Helper_AutoComplete extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Builds an AutoComplete ready input field.
     *
     * This view helper builds an input field with the {@link Zend_View_Helper_FormText} FormText
     * Helper and adds additional javascript to the jQuery stack to initialize an AutoComplete
     * field. Make sure you have set one out of the two following options: $params['data'] or
     * $params['url']. The first one accepts an array as data input to the autoComplete, the
     * second accepts an url, where the autoComplete content is returned from. For the format
     * see jQuery documentation.
     *
     * @link   http://docs.jquery.com/UI/Autocomplete
     * @throws ZendX_JQuery_Exception
     * @param  String $id
     * @param  String $value
     * @param  array $params
     * @param  array $attribs
     * @return String
     */
	public function autoComplete($id, $value = null, array $params = array(), array $attribs = array())
	{
        $attribs = $this->_prepareAttributes($id, $value, $attribs);

        if(!isset($params['data']) && !isset($params['url'])) {
            require_once "ZendX/JQuery/Exception.php";
            throw new ZendX_JQuery_Exception("Cannot construct AutoComplete field without specifying Parameters Data and Url");
        }

        if(isset($params['url'])){
        	$url = "'".$params['url']."'";
        	$filteredParams = array();
        	foreach($params as $k=>$v){
        		if($k == 'url') {
        			continue;
        		}
        		$filteredParams[$k] = $v;
        	}
        	$params = $filteredParams;
        } else {
        	$url = 'null';
        }
        
        $params = ZendX_JQuery::encodeJson($params);

        $js = sprintf('%s("#%s").autocomplete(%s, %s);',
            ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
            $attribs['id'],
            $url,
            $params
        );

        $this->jquery->addOnLoad($js);

        return $this->view->formText($id, $value, $attribs);
	}
}