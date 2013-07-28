<?php
abstract class HTMLPurifier_Filter_Video extends HTMLPurifier_Filter
{
    public $name = 'Video';

    protected function armorUrl($url)
    {
        return str_replace('--', '-&#45;', $url);
    }

    abstract protected function _getVideoParameters($matches);

    protected function postFilterCallback($matches)
    {
        $params = $this->_getVideoParameters($matches);
        $return = <<<RET

<object width="{$params['width']}" height="{$params['height']}" type="application/x-shockwave-flash" data="{$params['url']}">
<param name="movie" value="{$params['url']}" />
<param name="allowFullScreen" value="true" />
<param name="allowscriptaccess" value="always" />
<!--[if IE]><embed src="{$params['url']}" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="transparent" width="{$params['width']}" height="{$params['height']}" /><![endif]-->
</object>

RET;
        return $return;
    }
}