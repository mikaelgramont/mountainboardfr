<?php
class Media_Album_VideoMain extends Media_Album_Simple
{
	const ID = 2;
	const NAME = 'videomain';
	const ROUTE = 'video';
	
	protected static $_instance = null;
	
	public static function getInstance()
	{
		if(empty(self::$_instance)){
			$table = new Media_Album_Simple();
			self::$_instance = $table->find(self::ID)->current();
		}
		
		return self::$_instance;
	}

	/**
     * Name of the class representing a row
     *
     * @var string
     */
    protected $_rowClass = 'Media_Album_VideoMain_Row';	

}