<?php
class Lib_View_Helper_ShareButtons extends Zend_View_Helper_Abstract
{
	public function shareButtons()
	{
		return $this;
	}

	public function all($url, $class = '')
	{
		$classAttr = "";
		if($class){
			$classAttr = " class=\"{$class}\"";
		}

		$ret  = '<div id="shareButtons"'.$classAttr.'>'.PHP_EOL;
		$ret .= '<div id="facebookLikeButton"'.$classAttr.'>'.PHP_EOL.$this->facebook($url, $class).'</div>'.PHP_EOL;
		$ret .= '</div>'.PHP_EOL;

		return $ret;
	}

	public function facebook($url, $class, $width = 72, $itemType = '', $itemId = '')
	{
		$url = urlencode($url);

		if($itemType && $itemId){
			$ref = "&amp;ref={$itemType}_{$itemId}";
		} else {
			$ref = '';
		}

		$layout = 'box_count';
		$height = 65;

		if(strpos($class, 'horizontal') !== false){
			$layout = 'standard';
			$width = 450;
			$height = 30;
		}

		$return = <<<HTML
			<iframe src="https://www.facebook.com/plugins/like.php?href=$url&amp;layout={$layout}&amp;show_faces=false&amp;width=$width&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=$height{$ref}" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:{$width}px; height:{$height}px;" allowTransparency="true"></iframe>

HTML;

		return $return;
	}

	public function google($class = null)
	{
		$lang = $this->view->user->lang;
		$size = ($class == 'horizontal') ? '' : ' size="tall"';

// could try g.text = '{"lang":"$lang"}'
		$script = <<<SCRIPT
	(function(d, t) {
	var g = d.createElement(t),
		s = d.getElementsByTagName(t)[0];
	g.async = true;
	g.src = 'https://apis.google.com/js/plusone.js';
	g.appendChild(d.createTextNode('{"lang":"$lang"}'));
	s.parentNode.insertBefore(g, s);
	})(document, 'script');

SCRIPT;
		$this->view->JQuery()->addJavascript($script);

		return "<g:plusone$size></g:plusone>".PHP_EOL;
	}
}