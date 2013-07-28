<?php
class Lib_Mail
{
    public static function setDefaultMailTransport()
    {
        if(!GLOBAL_MAIL_USE_STMP){
            // By default, sendmail is used
            return;
        }
        

        $cache = Globals::getGlobalCache();
        $cacheId = 'mailTransport';
		if(ALLOW_CACHE || !($transport = $cache->load($cacheId))){
	        $config = array(
	        	'auth' => 'login',
				'username' => GLOBAL_SMTP_USERNAME,
				'password' => GLOBAL_SMTP_PASSWORD
			);

	        $transport = new Zend_Mail_Transport_Smtp(GLOBAL_SMTP_SERVER, $config);
			if(ALLOW_CACHE){
	        	$cache->save($transport, $cacheId);
			}
		}


        Zend_Mail::setDefaultTransport($transport);
    }
}