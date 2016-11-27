<?php
class Lib_View_Helper_ImageErrorHandler extends Zend_View_Helper_Abstract
{
	/*
	 * Hides images that error out.
	 * Ideally, we'd log them, but hey, that's what server logs are for.
	 */
	public function imageErrorHandler($cspNonce)
	{
		$log = LOG_IMG_ERRORS ? "console.log('Image error', e.target.src, e);" : "";

		$js = "
<script nonce=\"$cspNonce\">
document.body.addEventListener('error', function(e) {
	${log}
	if (e.target.tagName == 'IMG') {
		e.target.style = 'display:none';
	}
}, true);
</script>";
		return $js;
	}
}