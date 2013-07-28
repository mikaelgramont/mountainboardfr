<?php
class Lib_View_Helper_SWFObject extends Zend_View_Helper_Abstract
{
	protected $_defaultParameters = array(
		'allowscriptaccess' => 'always',
		'allowfullscreen ' => 'true',
	);

	public function SWFObject($id, $movie, $width, $height, $alternativeContent = null, $userParameters = array())
	{
		/**
		 * @todo:
		 *  - permettre d'ajouter des attributs optionnels à object
		 * @see http://code.google.com/p/swfobject/wiki/documentation
		 */

		// SWFObject library
		if(SWFOBJECT_USE_CDN){
			$swfObject = 'http://ajax.googleapis.com/ajax/libs/swfobject/'.SWFOBJECT_VERSION.'/swfobject.js';
			$this->view->headScript()->appendFile($swfObject);
		} else {
			$this->view->JQuery()->addJavascriptFile($this->view->asset()->script(SWFOBJECT_LOCAL_PATH));
		}


		// Object registration
		$inlineJs = 'swfobject.registerObject("'.$id.'", "'.SWFOBJECT_FLASH_PLAYER_VERSION.'", "'.SWFOBJECT_EXPRESS_INSTALL_LOCAL_PATH.'");';
		$this->view->headScript()->appendScript($inlineJs);

		// Alternative content
		if(is_null($alternativeContent)){
			$alternativeContent = '';
		}

		// Parameters
		$parameters = array_merge($this->_defaultParameters, $userParameters);
		$parameters['movie'] = $movie;
		$parametersString = '';
		foreach($parameters as $name => $value){
			$encodedValue = str_replace('&', '&amp;', $value);
			$parametersString .= "        <param name=\"$name\" value=\"$encodedValue\" />".PHP_EOL;
		}

		// Markup
		$movie = str_replace('&', '&amp;', $movie);
		$content = <<<SWFOBJECT

      <object id="{$id}" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{$width}" height="{$height}">
{$parametersString}
        <!--[if !IE]>-->
        <object type="application/x-shockwave-flash" data="{$movie}" width="{$width}" height="{$height}">
        <!--<![endif]-->
        {$alternativeContent}
        <!--[if !IE]>-->
        </object>
        <!--<![endif]-->
      </object>
SWFOBJECT;

		return $content;
	}
}