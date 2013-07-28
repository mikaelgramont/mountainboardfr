<?php
class Lib_View_Helper_Facebook_LikeBox extends Zend_View_Helper_Abstract
{
	public function facebook_LikeBox($url, $width = 328, $height = 255)
	{
		$url = urlencode($url);
		$return = <<<HTML
		<div id="facebookLikeBox">
			<iframe src="https://www.facebook.com/plugins/likebox.php?href=$url&amp;width=$width&amp;connections=10&amp;stream=false&amp;header=false&amp;height=$height" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:{$width}px; height:{$height}px;"></iframe>
		</div>
HTML;
		return $return;
	}
}
