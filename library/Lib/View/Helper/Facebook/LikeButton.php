<?php
class Lib_View_Helper_Facebook_LikeButton extends Zend_View_Helper_Abstract
{
	public function facebook_LikeButton($url, $class = '', $width = 72, $itemType = '', $itemId = '')
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

		if($class){
			$class = " class=\"{$class}\"";
		}
		$return = <<<HTML
		<div$class>
			<iframe src="https://www.facebook.com/plugins/like.php?href=$url&amp;layout={$layout}&amp;show_faces=false&amp;width=$width&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=$height{$ref}" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:{$width}px; height:{$height}px;" allowTransparency="true"></iframe>
		</div>
HTML;

		return $return;
	}
}