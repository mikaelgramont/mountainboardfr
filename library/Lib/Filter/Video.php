<?php
class Lib_Filter_Video implements Zend_Filter_Interface
{
	public function filter($value)
	{
    	// YOUTUBE
		$pre_regex = '#<object width="(\d{2,3})" height="(\d{2,3})"[^>]*>.+?value=\"http://www.youtube.com/v/([a-z0-9._\-]+)(.*)</object>#si';
        $pre_replace = '<div class="youtube-embed"><span class="width">\1</span><span class="height">\2</span>\3</div>';
        $value = preg_replace($pre_regex, $pre_replace, $value);

        $pre_regex = '#<iframe title="YouTube video player" width="(\d{2,3})" height="(\d{2,3})" src="http://www.youtube.com/embed/([a-z0-9._\-]+)"(.*)></iframe>#si';
        $pre_replace = '<div class="youtube-embed"><span class="width">\1</span><span class="height">\2</span>\3</div>';
        $value = preg_replace($pre_regex, $pre_replace, $value);

        $pre_regex = '#<iframe width="(\d{2,3})" height="(\d{2,3})" src="http://www.youtube.com/embed/([a-z0-9._\-]+)"(.*)></iframe>#si';
        $pre_replace = '<div class="youtube-embed"><span class="width">\1</span><span class="height">\2</span>\3</div>';
        $value = preg_replace($pre_regex, $pre_replace, $value);

        // DAILYMOTION
        $pre_regex = '#<object width="(\d{2,3})" height="(\d{2,3})"><param name="movie" value="http://www.dailymotion.com/swf/video/([a-z0-9._\-]+)(.*)#si';
        $pre_replace = '<div class="dailymotion-embed"><span class="width">\1</span><span class="height">\2</span>\3</div>';
        $value = preg_replace($pre_regex, $pre_replace, $value);

        $pre_regex = '#<object width="(\d{2,3})" height="(\d{2,3})"[^>]*>.+?value=\"http://www.dailymotion.com/swf/video/([a-z0-9._\-\?]+)(.*)</object>#si';
        $pre_replace = '<div class="dailymotion-embed"><span class="width">\1</span><span class="height">\2</span>\3</div>';
        $value = preg_replace($pre_regex, $pre_replace, $value);

        // VIMEO
        $pre_regex = '#<object width="(\d{2,3})" height="(\d{2,3})"[^>]*>.+?value=\"http://vimeo.com/moogaloop.swf\?clip_id=([0-9]+)(.*)</object>#si';
        $pre_replace = '<div class="vimeo-embed"><span class="width">\1</span><span class="height">\2</span>\3</div>';
        $value = preg_replace($pre_regex, $pre_replace, $value);

        $pre_regex =  '#<iframe src="http://player.vimeo.com/video/([0-9]+)(.*)" width="(\d{2,3})" height="(\d{2,3})" frameborder="0"(.*)></iframe>#si';
        $pre_replace = '<div class="vimeo-embed"><span class="width">\3</span><span class="height">\4</span>\1</div>';
        $value = preg_replace($pre_regex, $pre_replace, $value);

        return $value;
	}
}