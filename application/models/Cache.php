<?php
class Cache
{
	const MEMCACHED = 'Memcached';
	const APC = 'APC';
	const FILE = 'File';

	/**
	 * Factory method for a cache object
	 *
	 * @param string $method
	 * @return Zend_Cache_Core
	 */
	public static function factory($method = null, $options = array())
	{
		$frontOptions = array(
			'lifetime' => GLOBAL_CACHE_LIFETIME,
			'automatic_serialization' => true
		);

		if(empty($method)){
			$method = self::MEMCACHED;
		}

		if($method == self::MEMCACHED && !extension_loaded('memcache')){
			$method = self::APC;
        }

		if($method == self::APC && !ini_get("apc.enabled")){
			$method = self::FILE;
		}
		switch ($method){
			case self::APC:
            	$backOptions  = array();
				break;
			case self::FILE:
			default:
				$backOptions  = array();
				if(!array_key_exists('cache_dir', $options)){
					$backOptions['cache_dir'] = GLOBAL_CACHE_FILE_DIR;
				} else {
					$backOptions['cache_dir'] = $options['cache_dir'];
				}
				break;
			case 'Memcached':
				$backOptions = array(
					'servers' => array(
						array(
							'host' => '127.0.0.1',
							'port' => 11211,
							'persistent' =>  true,
							//'compression' => true,
						)
					)
				);
				break;
            }
	$cache = Zend_Cache::factory('Core', $method, $frontOptions, $backOptions);
		return $cache;
	}
}
