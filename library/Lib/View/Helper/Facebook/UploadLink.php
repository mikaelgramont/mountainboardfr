<?php
class Lib_View_Helper_Facebook_UploadLink extends Zend_View_Helper_Abstract
{
	public function facebook_UploadLink()
	{
		$uploadUrl = APP_URL.$this->view->url(array(),'uploadphotomain');
		$appId = FACEBOOK_APP_ID;
		$facebookUploadAuthUrl = "https://www.facebook.com/dialog/oauth?client_id=$appId&scope=user_photos&response_type=token&redirect_uri=".urlencode($uploadUrl);

		$return = '	<div class="addPhotoFacebook actionLinkContainer"><a class="dataLink facebook" href="'.$facebookUploadAuthUrl.'">'.ucfirst($this->view->translate('uploadFacebookPhoto')).'</a></div>'.PHP_EOL;
		return $return;
	}
}