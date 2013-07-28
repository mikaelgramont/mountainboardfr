<?php
abstract class Cache_Object extends Zend_Db_Table_Abstract
{
	/**
	 * Item type
	 *
	 * @var unknown_type
	 */
	protected $_itemType = 'cacheobject';

	public function saveDataInCache(Zend_Cache_Core $cache, $data, $id, $lifetime = null)
	{
		$cache->save($data, $id, array(), $lifetime);
	}

	/**
     * Return the item type of the object
     *
     * @return string
     */
    public function getItemType()
    {
        return strtolower($this->_itemType);
    }

	public function find($args = null )
	{
		if(!is_array($args)){
			$args = func_get_args();
		}
		
		if(!ALLOW_CACHE){
			return parent::find($args);
		}

		$cacheId = $this->getIndividualCacheId(implode('_', $args));
	    $cache = $this->getCache();
      	$result = $cache->load($cacheId);
	    if($result){
	    	$result->setTable($this);
	    	$result->rewind();
	    	return $result;
	    }

      	/**
      	 * Ugly hack!
      	 * Fortunately it's hidden away deep down here, but 
      	 * how can we get rid of it?
      	 */
	    $result = eval( 'return parent::find($args);');

    	$this->saveDataInCache($cache, $result, $cacheId);
		return $result;
	}

	/**
	 * This function returns the unique cache id for an object of this type.
	 *
	 * @param mixed $args
	 * @return string
	 */
	public function getIndividualCacheId($args = null)
	{
		if(is_array($args)){
			$index = implode('_', $args);
		} else {
			$index = $args;
		}

		$id = $this->getItemType() . $index;
		return $id;
	}

}