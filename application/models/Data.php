<?php
abstract class Data extends Cache_Object
{
    const ITEM_TYPE = 'data';

    const VALID = 'valid';
    const INVALID = 'invalid';

    const TONENEUTRAL = 'toneNeutral';
    const TONEJOKE = 'toneJoke';
    const TONEBRITISH = 'toneBritish';
    const TONELAMEJOKE = 'toneLameJoke';
    const TONEPEACE = 'tonePeace';
    const TONEHAPPY = 'toneHappy';
    const TONEGOTCHA = 'toneGotcha';
    const TONEUPSET = 'toneUpset';
    const TONESAD = 'toneSad';

    const EDITTYPE_FIRST_SAVE = "firstSave";
    const EDITTYPE_NEXT_SAVE = "nextSave";
    const EDITTYPE_EDIT = "edit";

    const ACTION_LIST = 'list';
    const ACTION_DISPLAY = 'display';
    const ACTION_EDIT = 'edit';
    const ACTION_DELETE = 'delete';

    public static $actions = array(
    	self::ACTION_LIST,
    	self::ACTION_DISPLAY,
    	self::ACTION_EDIT,
    	self::ACTION_DELETE,
    );

    protected $_itemType = 'data';

    /**
     * Name of the class representing a row
     *
     * @var string
     */
    protected $_rowClass = 'Data_Row';

    /**
     * @var array
     */
    protected $_referenceMap    = array(
        'LastEditor' => array(
            'columns'           => 'lastEditor',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'Submitter' => array(
            'columns'           => 'submitter',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        )
    );

    public static $tones = array(
        1 =>self::TONENEUTRAL,
        2 =>self::TONEJOKE,
        3 =>self::TONEBRITISH,
        4 =>self::TONELAMEJOKE,
        5 =>self::TONEPEACE,
        6 =>self::TONEHAPPY,
        7 =>self::TONEGOTCHA,
        8 =>self::TONEUPSET,
        9 =>self::TONESAD,
    );

    /**
     * Get the cache for this object
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
    	return Globals::getGlobalCache();
    }    
    
    public function getList($amountPerPage = 10, $page = 1, $orderBy = null, $orderByDir = null, $seeInvalidItems = false)
    {
        $orderBy = empty($orderBy) ? 'date' : $orderBy;
        $orderByDir = ($orderByDir != 'ASC') ? 'DESC' : 'ASC' ;
        $order = $orderBy.' '.$orderByDir;

        $where = "1";
        if(!$seeInvalidItems){
            $where .= " AND status = '".Data::VALID."'";
        }

        // Manage the offset of 1
        $page--;
        if($page < 0){
            $page = 0;
        }
        $offset = $amountPerPage * $page;

        $items = $this->fetchAll($where, $order, $amountPerPage, $offset);
        return $items;
    }

    /**
     * Get A list of random data
     *
     * @param integer $amount
     * @return array
     */
    public function getRandom($amount = 1)
    {
    	$amount = is_integer($amount) ? $amount : 1;
    	$data = $this->fetchAll('status = "'.self::VALID.'"', 'RAND()', $amount);
    	return $data;
    }

    /**
     * Get the latest data
     *
     * @param integer $amount
     * @return array
     */
    public function getLatest($amount = 1)
    {
    	$amount = is_integer($amount) ? $amount : 1;
    	$data = $this->fetchAll('status = "'.self::VALID.'"', 'id DESC', $amount);
    	return $data;
    }

	/**
	 * Return the 'parent' route for a given data type
	 *
	 * @param string $dataType
	 */
    public static function getParentRouteForDataType($dataType)
	{
        switch($dataType){
            case Constants_DataTypes::NEWS:
            case Constants_DataTypes::MEDIAALBUM:
            	$route = null;
            	break;
            case Constants_DataTypes::DOSSIER:
            	$route = 'homedossiers';
            	break;
            case Constants_DataTypes::TEST:
            	$route = 'listtests';
            	break;
            case Constants_DataTypes::TRICK:
            case Constants_DataTypes::SPOT:
			case Constants_DataTypes::DPT:
            case Constants_DataTypes::EVENT:
            case Constants_DataTypes::PHOTO:
            case Constants_DataTypes::VIDEO:
            case Constants_DataTypes::ALBUM:
            	$route = 'communityCat';
                break;
            case Constants_DataTypes::PRIVATEMESSAGE:
                $route = 'privatemessageshome';
                break;
            case Constants_DataTypes::BLOGPOST:
            	$route = 'listblogs';
            	break;
            case Constants_DataTypes::FORUMPOST:
			case Constants_DataTypes::FORUMTOPIC:
                $route = 'forums';
                break;
            default:
            	throw new Lib_Exception("Unknown data type '{$dataType}'");
            	break;
        }
        return $route;
	}

	/**
	 * Builds and return an item.
	 * Optionally throws an exception if the item does not exist,
	 * otherwise, returns a new one
	 *
	 * @param integer $itemId
	 * @param string $itemType
	 * @param boolean $exceptionOnEmpty
	 * @return mixed Data_Row|null
	 */
	public static function factory($itemId, $itemType, $exceptionOnEmpty = false)
	{
        if(empty($itemType)){
            throw new Lib_Exception_NotFound("No data type given");
        }

        switch($itemType){
        	case Constants_DataTypes::ALBUM:
        	case Constants_DataTypes::MEDIAALBUM:
        		$item = Media_Album_Factory::buildAlbumById($itemId);
        		break;
        	default:
        		$itemType = ucfirst(trim(Data::mapDataType($itemType)));
		        $table = new $itemType();
		
		        if(empty($itemId)){
		            $item = $table->fetchNew();
		            return $item;
		        }
		
		        $res = $table->find($itemId)->rewind();
		        $item = $res->current();
        		break;
        }
        if(empty($item) && $exceptionOnEmpty){
            throw new Lib_Exception_NotFound("data $itemId of type $itemType could not be found");
        }

        return $item;
	}

    /**
     * Maps a data type to its corresponding class
     *
     * @param string $dataType
     * @return string
     */
    public static function mapDataType($dataType)
    {
        switch($dataType){
            case Constants_DataTypes::NEWS:
            case Constants_DataTypes::DOSSIER:
            case Constants_DataTypes::TEST:
            case Constants_DataTypes::TRICK:
            case Constants_DataTypes::SPOT:
            case Constants_DataTypes::BLOG:
            case Constants_DataTypes::EVENT:
			case Constants_DataTypes::COMMENT:
			case Constants_DataTypes::DPT:
			case Constants_DataTypes::COUNTRY:
				$dataType = ucfirst($dataType);
                break;

			case Constants_DataTypes::BLOGLINK:
                $dataType = 'Blog_Link';
                break;
			case Constants_DataTypes::BLOGPOST:
                $dataType = 'Blog_Post';
                break;
            case Constants_DataTypes::PHOTO:
                $dataType = 'Media_Item_Photo';
                break;
            case Constants_DataTypes::VIDEO:
                $dataType = 'Media_Item_Video';
                break;
            case Constants_DataTypes::ALBUM:
            case Constants_DataTypes::MEDIAALBUM:
                $dataType = 'Media_Album';
                break;
            case Constants_DataTypes::FORUMPOST:
                $dataType = 'Forum_Post';
                break;
            case Constants_DataTypes::PRIVATEMESSAGE:
                $dataType = 'PrivateMessage';
                break;
            case Constants_DataTypes::FORUMTOPIC:
                $dataType = 'Forum_Topic';
                break;
            case User::ITEM_TYPE:
                $dataType = 'User';
                break;
            default:
            	throw new Lib_Exception("Unknow data type '{$dataType}'");
            	break;
        }
        return $dataType;
    }
}