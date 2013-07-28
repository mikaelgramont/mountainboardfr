<?php
class Lib_Form_Element_Riders extends Zend_Form_Element_Text
{
	protected $_media;

	/**
     * Does the element represent an array?
     * @var bool
     */
    protected $_isArray = true;	
    
    protected $_list;
    
	public $helper = 'riderList';
	
	public function __construct($spec, $media = null, $options = null)
	{
		parent::__construct($spec, $options);
		$this->_media = $media;
		$this->_list = $this->buildRiderList();
	}

	/**
	 * From a comma-separated list of names, this function builds an array of [username => userId]'s.
	 * If a user does not exist, default value returned is 0.
	 *
	 * @return array
	 */
	public function getNames($defaultReturnValue = 0)
	{
		$rawMembers = $this->getValue();
		if(empty($rawMembers)){
			return array();
		}
		$usernames = array();
		foreach($rawMembers as $rawMember){
			$usernames = array_merge(explode(',', $rawMember), $usernames);
		}
		
		$array = array();
		$list = array();
		foreach($usernames as $username){
			$username = trim($username);
			$array[strtolower($username)] = $defaultReturnValue;
			$list[] = $username;
		}
		
		$where = User::COLUMN_USERNAME . " IN ('" . implode("','", $list) ."')";
		$table = new User();
		$users = $table->fetchAll($where);
		foreach($users as $user){
			$username = $user->{User::COLUMN_USERNAME};
			$array[strtolower($username)] = $user->{User::COLUMN_USERID};
		}
		return $array;
	}
	
	/**
	 * Returns a readable, comma-separated list of names
	 *
	 * @return string
	 */
	public function getValueFromDatabase()
	{
		$return = $this->_media->getRiderNamesInMedia();
		return $return;
	}
	
	public function buildRiderList($glue = ',')
	{
		if(ALLOW_CACHE){
			$cacheId = User::VALID_USER_LIST_CACHE_ID;
        	$cache = Globals::getGlobalCache();
		}
	    if(ALLOW_CACHE || !($list = $cache->load($cacheId))){
	        
			$table = new User();
	        $statusList = array(
	         	User::STATUS_ADMIN,
	           	User::STATUS_EDITOR,
	           	User::STATUS_WRITER,
	           	User::STATUS_MEMBER,
			);
			$string = implode("','", $statusList);
	       	$users = $table->fetchAll("status IN ('$string')", User::COLUMN_USERNAME .' ASC'); 
	        
	        $list = array();
	        foreach($users as $user){
	        	$title = $user->getTitle();
	        	if($title == Utils::cleanString($title, false)){
	        		$list[] = $title;
	        	}
	        }
	        if(ALLOW_CACHE){
	        	$cache->save($list, $cacheId);
	        }
        }
        
        return implode($glue, $list);
	}
	
	public function getList()
	{
		return $this->_list;
	}

	public function getHint()
	{
		return 'ridersHint';
	}
}