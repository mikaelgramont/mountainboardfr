<?php 
class Lib_View_Helper_GoogleAnalytics extends Zend_View_Helper_Abstract
{
    public function googleAnalytics($useAsynchronous = true)
    {
    	$return = $useAsynchronous ? $this->_asynchronous() : $this->_synchronous();
    	return $return;
    }
    
    protected function _synchronous()
    {
        $trackingCode = ANALYTICS_CODE;
    	$js = <<<JS
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try{
var pageTracker = _gat._getTracker("UA-$trackingCode");
pageTracker._trackPageview();
} catch(err) {}
</script>        
JS;
		return $js;	
    }
    
    protected function _asynchronous()
    {
        $trackingCode = ANALYTICS_CODE;
        $cookieDomain = COOKIE_DOMAIN;
        
    	$js = <<<JS
<script type="text/javascript">
   	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '$trackingCode']);
	_gaq.push(['_setDomainName', '$cookieDomain']);
	_gaq.push(['_trackPageview']);
	_gaq.push(['_trackPageLoadTime']);
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>        
	
JS;
		return $js;	
    }    
}
