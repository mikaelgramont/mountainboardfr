<?php
/**
 * @see http://www.aaronpeters.nl/blog/google-plus1-button-performance-review
 */
class Lib_View_Helper_Google_PlusOne extends Zend_View_Helper_Abstract
{
	public function google_PlusOne()
	{
		$lang = $this->view->user->lang;
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

		return '<g:plusone></g:plusone>'.PHP_EOL;
	}
}