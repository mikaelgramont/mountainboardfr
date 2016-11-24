<?php
class Lib_View_Helper_ImageErrorHandler extends Zend_View_Helper_Abstract
{
	/*
	 * Hides images that error out.
	 * Ideally, we'd log them, but hey, that's what server logs are for.
	 */
	public function imageErrorHandler()
	{
		$js = "
document.body.addEventListener('error', function(e) {
	if (e.target.tagName=='IMG') {
		e.target.style='display:none';
	}
}, true);";
    $this->view->jQuery()->addOnLoad($js);

	}
}