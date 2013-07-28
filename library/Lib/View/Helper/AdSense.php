<?php
class Lib_View_Helper_AdSense extends Zend_View_Helper_Abstract
{
	public function adSense()
	{
		$test = true;
		
		$content = <<<ADSENSE
<script type="text/javascript">
	<!--
	google_adtest = "on";         // new line
	google_ad_client = "pub-0000000000000000";
	google_alternate_color = "ffffff";
	google_ad_width = 468;
	google_ad_height = 60;
	google_ad_format = "468x60_as";
	google_ad_type = "text_image";
	google_ad_channel = "0000000000";
	//-->
</script>
<div id="promos_ads">
TOTO
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</div>		
ADSENSE;
		return $content;
	}
}