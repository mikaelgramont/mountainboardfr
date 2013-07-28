<?php
abstract class Cache_Object_Row extends Zend_Db_Table_Row
{
	/**
	 * Clear all cache entries related to this object
	 *
	 */
	abstract public function clearCache();

    public function __wakeup()
    {
    	if(empty($this->_table)){
    		$this->setTable(new $this->_tableClass);
    	}
    }

	protected function _doUpdate()
	{
		$return = parent::_doUpdate();
		if(ALLOW_CACHE){
			$this->clearCache();
		}
		return $return;
	}

	protected function _doInsert()
	{
		$return = parent::_doInsert();
		if(ALLOW_CACHE){
			$this->clearCache();
		}
		return $return;
	}

	protected function _postDelete()
	{
		$return = parent::_postDelete();
		if(ALLOW_CACHE){
			$this->clearCache();
		}
		return $return;
	}

	public function getCache()
	{
		$cache = $this->getTable()->getCache();
		return $cache;
	}

	protected function _getTable()
	{
		if(ALLOW_CACHE){
			if(empty($this->_table)){
	    		$this->setTable(new $this->_tableClass);
	    	}
		}
		return parent::_getTable();
	}

	public function getRowClass()
	{
		return $this->_rowClass;
	}

	public function findParentRow($parentTable, $ruleKey = null, Zend_Db_Table_Select $select = null)
	{
		if(ALLOW_CACHE){
			if(!empty($select)){
				throw new Lib_Exception("Caching of findParentRow is not supported yet for select");
			}
			$cache = $this->getCache();
			$cacheId = $this->getParentRowCacheId($parentTable, $ruleKey);
		    $result = $cache->load($cacheId);
		    if($result){
		    	$table = new $parentTable;
		    	$result->setTable($table);
		    	return $result;
		    }
		}
		$result = parent::findParentRow($parentTable, $ruleKey, $select);
		
		if(!ALLOW_CACHE){
			return $result;
		}
		
    	if($result instanceof Zend_Db_Table_Rowset){
			$result->rewind();
		}

    	$this->getTable()->saveDataInCache($cache, $result, $cacheId);

		return $result;
	}

	public function getParentRowCacheId($parentTable, $ruleKey = null)
	{
		if(!empty($ruleKey)){
			$id = $ruleKey;
		} else {
			$id = $parentTable;
		}

		$id .= '_For_'.ucfirst($this->getItemType()).$this->getId();
		return $id;
	}
	
	public function getIndividualCacheId($args = null)
	{
		$id = $this->getTable()->getIndividualCacheId($this->getId());
		return $id;
	}	
}
