<?php
class Utils
{
    public static function removeAccents($string)
    {
        $array = array('&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&agrave;', '&aacute;',
                       '&acirc;','&atilde;', '&auml;', '&aring;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;',
                       '&Ouml;', '&Oslash;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&oslash;',
                       '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&egrave;', '&eacute;', '&ecirc;', '&euml;',
                       '&Ccedil;', '&ccedil;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&igrave;','&iacute;',
                       '&icirc;', '&iuml;', '&Ugrave;', '&Uacute;', '&Ucirc;', '&Uuml;', '&ugrave;', '&uacute;',
                       '&ucirc;', '&uuml;', '&yuml;', '&Ntilde;', '&ntilde;');
        $replace = array('a','a','a','a','a','a','a','a','a','a','a','a','o','o','o','o','o','o','o','o','o','o','o','o',
                         'e','e','e','e','e','e','e','e','c','c','i','i','i','i','i','i','i','i','u','u','u','u','u','u',
                         'u','u','y','n','n');
        $return = htmlentities($string,ENT_NOQUOTES, 'UTF-8');
        $return = str_replace($array, $replace, $return);
        $return = html_entity_decode($return,ENT_NOQUOTES, 'UTF-8');
        return $return;
    }

    public static function cleanString($string, $cleanSpace = true)
    {
        $clean = self::removeAccents($string);
        $from = "<>\n\r";
        $to   = "------";
        
        if($cleanSpace){
        	$from .= ' ';
        	$to .= '-';
        }
        
        $from .= "²&~\"#'{([|`_\\^@)]°=+}¨\$£¤%*?,.;/:!§€…";
        $to .= "--------------------------------------e-";
        
        $clean = strtr($clean, $from, $to);
        $clean = preg_replace("/(-){2,}/", "-", $clean);
        $clean = trim($clean, ' -');
        return $clean;
    }

    /**
     * Create a url-safe string based on the input
     *
     * @param string $string
     * @return string
     */
    public static function cleanStringForUrl($string)
    {
        $clean = self::escape($string);
        $clean = self::cleanString($clean);
        $clean = strtolower($clean);
        return $clean;
    }

    public static function cleanStringForTag($string)
    {
        $clean = self::cleanString($string);
        $clean = self::filterForbiddenWords($clean);
        $clean = strtolower($clean);
        return $clean;
    }

    public static function cleanStringForFilename($string)
    {
          $clean = self::escape($string);
          $clean = self::cleanString($clean);
          $clean = self::filterForbiddenWords($clean);
          $clean = strtolower($clean);
          return $clean;
    }

    public static function makeDateReadable($date)
    {
        $a = substr($date, 8, 2);
        $b = substr($date, 5, 2); $b = Constants::$months[(int)$b];
        $c = substr($date, 0, 4);
        return $a.' '.$b.' '.$c;
    }

    public static function makeDateTimeReadable($date)
    {
        $a = substr($date, 8, 2);
        $b = substr($date, 5, 2); $b = Constants::$months[(int)$b];
        $c = substr($date, 0, 4);
        $d = substr($date, 11, 2);
        $e = substr($date, 14, 2);
        $f = substr($date, 17, 2);

        return $a.' '.$b.' '.$c. ' &agrave; '.$d.':'.$e.':'.$f;
    }

    public static function filter($data)
    {
        if(!is_array($data)){
            $return = htmlentities($data, ENT_QUOTES, 'UTF-8');
        } else {
            $return = array();
            foreach($data as $key=>$value){
                $return[$key] = self::filter($value);
            }
        }
        return $return;
    }

    public static function escape($data)
    {
        if(!is_array($data)){
            $return = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
        } else {
            $return = array();
            foreach($data as $key=>$value){
                $return[$key] = self::escape($value);
            }
        }
        return $return;
    }

    public static function filterFromPrototype($input)
    {
        return htmlentities(stripslashes($input), ENT_QUOTES);
    }

    /**
     * Allow direct interaction with prototype.js
     * content of array:
     *  - errors
     *  - msg
     *  - url (opt)
     *  - parms (opt)
     */
    public static function sendToPrototype($array)
    {
        Zend_Loader::loadClass('Zend_Json');
        $output = '('.Zend_Json::encode($array).')';
        //header("X-JSON: $output");
        echo $output;
        exit();
    }

    public static function filterForbiddenWords($string, $file = '')
    {
        if($file == '') $file = Constants::$wordfilters;
        if(!@is_file($file)) return $string;

        $content = file_get_contents($file);
        $filters = strtr($content,
                       "\n\r ",
                       ",,,");
        $preg = array();
        $filters = explode(',', $filters);
        foreach($filters as $filter){
            $preg[] = "/(\s)+".$filter."(\s)+/";
        }
        $clean = preg_replace ($preg, ' ', $string);
        return $clean;
    }

    /**
     * Sets a cookie by adding the corresponding header to the main
     * response object
     *
     * @param string $name
     * @param string $value
     */
    public static function setCookie($name, $value)
    {
        $time = time() + 3600 * 24 * 365;
        $cookie = new Zend_Http_Cookie($name, $value, '.'.COOKIE_DOMAIN, $time, '/');
        Zend_Controller_Front::getInstance()->getResponse()->setHeader('Set-Cookie',
            $cookie->__toString(). ' domain='.$cookie->getDomain().'; path=/; expires='.date('r',$cookie->getExpiryTime()).';'
        );
    }

    /**
     * Deletes a cookie by adding the corresponding header to the main
     * response object
     *
     * @param string $name
     */
    public static function deleteCookie($name)
    {
        $time = time() - 3600 * 24 * 365;
        $cookie = new Zend_Http_Cookie($name, '', '.'.COOKIE_DOMAIN, $time, '/');

        Zend_Controller_Front::getInstance()->getResponse()->setHeader('Set-Cookie',
            $cookie->__toString(). ' domain='.$cookie->getDomain().'; expires='.date('r',$cookie->getExpiryTime()).';'
        );
    }

    public static function getRandomKey($length = 32)
    {
        $from = '0123456789abcdefghijklmnopqrstuvwxyz';

        $return = '';
        for($i = 0; $i < $length; $i++){
            $index = rand(0,35);
            $return .= $from[$index];
        }
        return $return;
    }

	/**
	 * Get string length, multibyte.
	 *
	 * @param   string  $t Any string content
	 * @param   string  $encoding Charset encoding
	 * @return  int     string length
	 */
	public static function strlen($t, $encoding = 'UTF-8')
	{
		if (function_exists('mb_strlen')){
			$length = mb_strlen($t, $encoding);
		} else {
			$length = strlen(utf8_decode($t));
		}
		return $length;
	}

	/**
	 * Get substring, multibyte.
	 *
	 * @param   string  $t Any string content
	 * @param   int 	$length substring length
	 * @param   string  $encoding Charset encoding
	 * @return  string  string length
	 */
	public static function substr($t, $length, $encoding = 'UTF-8')
	{
		if (function_exists('mb_strlen')){
			$substr = mb_substr($t, 0, $length, $encoding);
		} else {
			$substr = utf8_encode(substr(utf8_decode($t), 0 , $length, $encoding));
		}
		return $substr;
	}

	public static function getCompleteUrl()
	{
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		if(!isset($_SERVER["SERVER_PROTOCOL"])){
			return '';
		}	
		$protocol = self::strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];            
	}
	
	public static function strleft($s1, $s2)
	{
		return substr($s1, 0, strpos($s1, $s2));
	}

	public static function getHost($ip)
	{
		$testar = explode('.',$ip);
		if (count($testar)!=4) {
			return $ip;
		}
		for ($i=0;$i<4;++$i){
	  		if (!is_numeric($testar[$i])){
	   			return $ip;
	  		}
		}	
		$host = `host $ip`;
		if(!$host){
			return $ip;
		}
		
		$parts = explode(' ', $host);
		$return = array_pop($parts);
		return $return;		
	} 

}
