<?php
class HTMLPurifier_Filter_Vimeo extends HTMLPurifier_Filter_Video
{
    public $name = 'Vimeo';

    protected function _getVideoParameters($matches)
    {
        $params = array(
            'url' => 'http://vimeo.com/moogaloop.swf?clip_id='.$this->armorUrl($matches[1]).'&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1',
            'width' => 400,
            'height' => 300
        );
        return $params;
    }

    public function preFilter($html, $config, $context) {
    	$pre_regex = '#<object width="(\d{2,3})" height="(\d{2,3})"[^>]*>.+?value=\"http://vimeo.com/moogaloop.swf\?clip_id=([0-9]+)(.*)</object>#si';
        $pre_replace = '<div class="vimeo-embed"><span class="width">\1</span><span class="height">\2</span>\3</div>';
        $return = preg_replace($pre_regex, $pre_replace, $html);
        return $return;
    }
/*
    public function postFilter($html, $config, $context) {
        $post_regex = '#<div width="([0-9]+)" height="([0-9]+)" class="vimeo-embed">([A-Za-z0-9\-_]+)</div>#';
        $return = preg_replace_callback($post_regex, array($this, 'postFilterCallback'), $html);
        return $return;
    }    
*/

}