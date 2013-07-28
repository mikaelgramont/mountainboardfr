<?php
class HTMLPurifier_Filter_DailyMotion extends HTMLPurifier_Filter_Video
{
    public $name = 'DailyMotion';

    protected function _getVideoParameters($matches)
    {
        $params = array(
            'url' => 'http://www.dailymotion.com/video/'.$this->armorUrl($matches[1]).'&amp;related=1',
            'width' => 480,
            'height' => 381
        );
        return $params;
    }
    
    public function preFilter($html, $config, $context) {
    	$pre_regex = '#<object width="(\d{2,3})" height="(\d{2,3})"[^>]*>.+?value=\"http://www.dailymotion.com/swf/([a-z0-9._\-\?]+)(.*)</object>#si';
        $pre_replace = '<div class="dailymotion-embed"><span class="width">\1</span><span class="height">\2</span>\3</div>';
        $return = preg_replace($pre_regex, $pre_replace, $html);
        return $return;
    }
/*
    public function postFilter($html, $config, $context) {
        $post_regex = '#<div width="([0-9]+)" height="([0-9]+)" class="dailymotion-embed">([A-Za-z0-9\-_]+)</div>#';
        $return = preg_replace_callback($post_regex, array($this, 'postFilterCallback'), $html);
        return $return;
    }      
*/
}