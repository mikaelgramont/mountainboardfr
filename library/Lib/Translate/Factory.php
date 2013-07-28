<?php
/**
 * Factory class for a Zend_Translate object
 * Initialize the Zend_Translate object with the desired
 * language according to locale, preference.
 */
class Lib_Translate_Factory
{
    /**
     * Build a configured Zend_Translate object
     *
     * @param string $updateTo
     * @return Zend_Translate
     */
    public static function build($updateTo = null)
    {
    	if(ALLOW_CACHE){
    		Zend_Translate::setCache(Globals::getGlobalCache());
    	}
        $translate = self::_getTranslateObject();

        // Pick a language
        $chosenLang = self::_pickLanguage($updateTo);

        // Check for availability of chosen language
        if(!$translate->isAvailable($chosenLang)){
            // If default language is not available, throw an exception
            if(!$translate->isAvailable(GLOBAL_LANG_DEFAULT)){
                throw new Lib_Exception("Language {$chosenLang} is not available");
            }

            $chosenLang = GLOBAL_LANG_DEFAULT;
        }

        $_SESSION['lang'] = $chosenLang;
        $translate->setLocale($chosenLang);
        Zend_Registry::set('Zend_Locale', $chosenLang);
        return $translate;
    }

    /**
     * Build a Zend_Translate object, and give it all
     * available languages.
     *
     * @return Zend_Translate
     */
    private static function _getTranslateObject()
    {
        $mainLangDirectory = GLOBAL_LANG_PATH;
        $defaultLanguage = GLOBAL_LANG_DEFAULT;

        $translate = new Zend_Translate('array', $mainLangDirectory.$defaultLanguage.DIRECTORY_SEPARATOR.'lang_main.php', $defaultLanguage);

        $availableLanguages = array();

        $languageDirectories = self::_getLanguageDirectories($mainLangDirectory);

        foreach($languageDirectories as $lang){
            if($lang == $defaultLanguage){
                continue;
            }

            $target = $mainLangDirectory.$lang.DIRECTORY_SEPARATOR.'lang_main.php';
            if(file_exists($target)){
                $translate->addTranslation($target, $lang);
            }
        }

        return $translate;
    }

    /**
     * Return a list of all available language directories
     *
     * @param string $path
     * @return array
     */
    private static function _getLanguageDirectories($path)
    {
        $directories = array();
        $dir = new DirectoryIterator($path);
        foreach($dir as $file){
            $filename = $file->getFilename();
            if($file->isDot() || !$file->isDir() || substr($filename, 0, 1) == '.'){
                continue;
            }

            $directories[] = $filename;
        }

        return $directories;
    }

    /**
     * Return the locale string (short) for chosen language
     *
     * @return string
     */
    private static function _pickLanguage($updateTo = null)
    {
        $user = Globals::getUser();

        if($updateTo !== null){
        	$return = $updateTo;
        } elseif(isset($_SESSION['lang'])){
            $return = $_SESSION['lang'];
        } elseif(self::_isRobot()){
        	$return = Globals::getDefaultSiteLanguage();
        } elseif(!empty($user) && $user->isLoggedIn() && isset($user->lang) && !empty($user->lang)){
            // Logged-in user with their own preference
            $return = $user->lang;
        } elseif($subdomainLang = Globals::getSubdomainLanguage()) {
            $return =  $subdomainLang;
        } else {
            // Automatic locale detection
            $locale = new Zend_Locale();
            $strLocale = $locale->toString();
            $parts = explode('_', $strLocale);
            $localeLang = $parts[0];
            $return =  $localeLang;
        }

        $supportedLanguages = explode(',', GLOBAL_SUPPORTED_LANG);
        if(!in_array($return, $supportedLanguages)){
        	Globals::getLogger()->error("Language not supported: '$return'. Supported languages: ".GLOBAL_SUPPORTED_LANG, Zend_Log::INFO);
        	
        	// Pick English in case of unsupported language
        	$return = 'en';
        	
        }

        return $return;
    }

	private static function _isRobot()
	{
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'local';
		$host = Utils::getHost($ip);
		
		$robots = array(
			'googlebot.com',
			'search.msn.com',
			'crawl.yahoo.net',
			'dotnetdotcom.org'
		);
		
		foreach($robots as $robot){
			if(strpos($robot, $host) !== false){
				return true;
			}
		}
		
		return false;
	}
}