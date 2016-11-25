<?php
class Lib_View_Helper_Google_Analytics extends Zend_View_Helper_Abstract
{
    public function google_Analytics($useAsynchronous = true)
    {
    	$this->view->JQuery()->addJavascript($useAsynchronous ? $this->_asynchronous() : $this->_synchronous());
    }

    protected function _synchronous()
    {
        $trackingCode = ANALYTICS_CODE;
    	$js = <<<JS
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
	try{
	var pageTracker = _gat._getTracker("UA-$trackingCode");
	pageTracker._trackPageview();
	} catch(err) {}

JS;
		return $js;
    }

    protected function _asynchronous()
    {
        $trackingCode = ANALYTICS_CODE;

    	$js = <<<JS
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-$trackingCode', 'auto');
	ga('send', 'pageview');
JS;
		return $js;
    }
}
