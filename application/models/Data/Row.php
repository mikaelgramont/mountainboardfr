<?php
/**
 * Abstract class representing an object that can be interesting for a user
 * (that may be associated to a page).
 *
 */
abstract class Data_Row extends Cache_Object_Row implements Data_Row_DataInterface
{
	/**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = Constants_DataTypes::DATA;

    /**
     * Name of the column that holds the 'title' data
     *
     * @var string
     */
    protected $_titleColumn = 'title';

    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'displaydata';

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'editdata';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'createdata';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deletedata';

    /**
     * Name of the route used to construct list urls
     *
     * @var string
     */
    protected $_listRoute = 'listdata';

    /**
     * Name of the route that the user will be redirected to
     * upon deletion of this object
     *
     * @var string
     */
    protected $_defaultDeleteRedirectRoute = 'defaults';

	/**
     * Array of tags associated to the current object
     *
     * @var array
     */
    protected $_arrTags = null;

    /**
     * Default category
     *
     * @var int
     */
    protected $_category = Category::COMMUNITY;

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::NONE;

    /**
     * Default creation category
     *
     * @var int
     */
    protected $_creationCategory = Category::COMMUNITY;

	/**
     * Default creation subcategory
     *
     * @var int
     */
    protected $_creationSubCategory = SubCategory::NONE;

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Data_Form';

    /**
     * Whether this object is fully saved with one form (true),
     * or if we must go through two passes (false).
     *
     * @var boolean
     */
    protected $_onePassSubmit = true;

    /**
     * Name of the class of form used to submit the title of this object
     * the very first time the object is going to be saved
     *
     * @var string
     */
    protected $_subForm1Class = null;

    /**
     * Name of the class of form used to edit the rest of the attributes
     * of this object before it is activated
     *
     * @var string
     */
    protected $_subForm2Class = null;

    /**
     * Default value of notification
     *
     * @var boolean
     */
    protected $_defaultNotification = true;

    /**
     * Whether or not we should create an album when this
     * item is saved
     *
     * @var Boolean
     */
    protected $_createAlbumOnSave = false;

    /**
     * The type of album attached to this object:
     * simple or aggregation
     *
     * @var string
     */
    protected $_albumType = Media_Album::TYPE_AGGREGATE;

    /**
     * The type of album attached to this object:
     * simple or aggregation
     *
     * @var string
     */
    protected $_albumAccess = Media_Album::ACCESS_PUBLIC;

    /**
     * Whether or not we should create a folder when this
     * item is saved
     *
     * @var Boolean
     */
    protected $_createFolderOnSave = false;

    /**
     * Indicates whether the title is found in another table (true)
     * or directly here (false)
     *
     * @var boolean
     */
    protected $_isTitleTranslated = true;

    /**
     * Indicates whether the description is found in another table (true)
     * or directly here (false)
     *
     * @var boolean
     */
    protected $_isDescriptionTranslated = true;

    /**
     * This variable serves as a buffer to the translated texts
     *
     * @var array
     */
    protected $_translationBuffer = array();

    /*
     * Indicates whether this item should be announced to users,
     * when it is submitted, or if it should be a silent addition
     * @var boolean
     */
    protected $_notification;

    /**
     * Language this item is presented in
     *
     * @var string
     */
    protected $_lang;

    /**
     * Name of the layouts used to display this item
     *
     * @var string
     */
    protected $_layouts = array(
    	Data::ACTION_LIST => 'two-columns',
    	Data::ACTION_DISPLAY => 'one-column',
    );

    public function __construct(array $config = array())
    {
    	$this->setNotification($this->_defaultNotification);

    	if(array_key_exists('table', $config) && is_null($config['table'])){
    		// Builds the name of the table if it is null (ugly !)
    		$tableClass = str_replace('_Row', '', get_class($this));
    		$config['table'] = new $tableClass;
    	}
    	parent::__construct($config);
    }

    /**
     * Some fields are saved directly from the table associated
     * to this class, while some may go into the translated text
     * table.
     *
     * @param string $columnName
     * @return string
     */
    public function __set($columnName, $value)
    {
		if(!$this->_isTranslated($columnName)){
		   	parent::__set($columnName, $value);
			return;
		}

		$this->_translationBuffer[$columnName] = $value;
    }

    /**
     * Flushes translated texts to the database
     */
    protected function _saveTranslatedTexts()
    {
		$lang = Zend_Registry::get('Zend_Locale');

		foreach($this->_translationBuffer as $columnName => $value){
			$textRow = $this->getTranslatedText($lang, $columnName, true);
			$textRow->text = $value;
			$textRow->save();
		}
    }

    /**
     * Returns the translated text row of given type and language,
     * returns the default language if the requested one does not exist
     * creates a blank one if none exist.
     *
     * @param string $lang
     * @param string $columnName
     * @return Zend_Db_Table_Row_Abstract
     */
    public function getTranslatedText($lang, $columnName, $write = false)
    {
		$textRow = null;
    	$rowset = $this->getAllTranslatedTexts($lang);

    	foreach($rowset as $row){
			if($row->type == $columnName && $row->lang == $lang){
				$textRow = $row;
				break;
			}
		}

		/**
		 * @todo: permettre de creer de nouvelles entrees pour la langue actuelle
		 * si ça n'existe pas.
		 */

    	if(!$textRow){
			if(!$write){
	    		// Reading non-existing translations: try default language
	    		foreach($rowset as $row){
					if($row->type == $columnName && $row->lang == GLOBAL_LANG_DEFAULT){
						$textRow = $row;
						break;
					}
				}
    			if(!$textRow){
    				// Reading non-existing translations: try whatever's available first
		    		foreach($rowset as $row){
						if($row->type == $columnName){
							$textRow = $row;
							break;
						}
					}
    			}
			} else {
				// Saving new translation: nothing to do
			}
    	}

		if(empty($textRow)){
			$table = new Data_TranslatedText();
			$textRow = $table->fetchNew();
			$textRow->id = $this->id;
			$textRow->itemType = $this->getItemType();
			$textRow->lang = $lang;
			$textRow->type = $columnName;
		}

		return $textRow;
    }

    /**
     * Returns translated texts relative to this object, of
     * all types and languages
     *
     * @param string $lang
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getAllTranslatedTexts($lang)
    {
    	if(!ALLOW_CACHE){
			$translatedTexts = Data_TranslatedText::getAllTranslatedTexts($this->id, $this->getItemType());
			return $translatedTexts;
    	}

    	$cacheId = $this->_getTranslatedTextsCacheId();
		$cache = $this->getCache();
		$translatedTexts = $cache->load($cacheId);
		$table = new Data_TranslatedText();
	    if($translatedTexts === false){
			$translatedTexts = Data_TranslatedText::getAllTranslatedTexts($this->id, $this->getItemType());
			$this->getTable()->saveDataInCache($cache, $translatedTexts, $cacheId);
		} else {
			$translatedTexts->setTable($table);
		}

    	return $translatedTexts;
    }

    /**
     * Returns the cache id for all translated texts relative to this
     * object.
     *
     * @return string
     */
    protected function _getTranslatedTextsCacheId()
    {
    	$cacheId = 'translatedTextsFor_'.$this->getItemType().$this->getId();
    	return $cacheId;
    }

    /**
     * Some fields are fetched directly from the table associated
     * to this class, while some may come from the translated text
     * table.
     *
     * @param string $columnName
     * @return string
     */
    public function __get($columnName)
    {
		if(!$this->_isTranslated($columnName)){
    		$value = parent::__get($columnName);
			return $value;
		}

		if(isset($this->_translationBuffer[$columnName])){
			$value = $this->_translationBuffer[$columnName];
		} else {
			$lang = Zend_Registry::get('Zend_Locale');

			$textRow = $this->getTranslatedText($lang, $columnName);
			if(empty($textRow)){
				return null;
			}
			$value = $textRow->text;
		}
		return $value;
    }

    public function getId()
    {
    	return $this->id;
    }

    /**
	 * Sets notification flag to the value corresponding
	 * to the boolean. This defines whether this item
	 * will be announced after it is submitted.
     *
     * @param boolean $value
     */
    public function setNotification($value = false)
    {
    	if(!$value){
    		$this->_notification = Item_Row::NOTIFICATION_SILENT;
    	} else {
    		$this->_notification = Item_Row::NOTIFICATION_ANNOUNCE;
    	}
    }

    /**
	 * Sets the flag to tell whether we need to create an album
	 * on save.
     *
     * @param boolean $value
     */
    public function createAlbumOnSave($value = false)
    {
   		$this->_createAlbumOnSave = $value;
    }

	/**
     * Returns the comments associated to the current object,
     * depending on current user and acl rules.
     *
     * @param User_Row $user
     * @param Lib_Acl $acl
     * @return array
     */
    public function getComments(User_Row $user, Lib_Acl $acl)
    {
        $parentType = strtolower($this->getItemType());
        $where = "parentId = $this->id AND parentType = '$parentType'";
        $result = Data_Utils::getList($user, $acl, 'comment', $where);
        $select = $result['select'];
		$table = $select->getTable();

        if(!ALLOW_CACHE){
        	$comments = $table->fetchAll($select);
        	return $comments;
        }

		$cacheId = $this->_getCommentsCacheId();
        $cache = $this->getCache();

        $comments = $cache->load($cacheId);
	    if($comments === false){
			$comments = $table->fetchAll($select);
			$this->getTable()->saveDataInCache($cache, $comments, $cacheId);
		} else {
			$comments->setTable($table);
		}

        return $comments;
    }

    /**
     * Clears all cache entries related to this object
     */
    public function clearCache()
    {
    	$cache = $this->getCache();
    	$cacheIds = $this->_getCacheIdsForClear();
    	foreach($cacheIds as $cacheId){
    		$cache->remove($cacheId);
    	}
    }

    /**
     * Returns a list of all cache id's that must be emptied
     * when saving/deleting this object.
     */
    protected function _getCacheIdsForClear()
    {
		$return = array(
			$this->getIndividualCacheId(),
    		$this->_getCommentsCacheId(),
    		$this->_getTranslatedTextsCacheId(),
    		$this->_getTagsCacheId(),
    		$this->_getViewsCacheId(),
    	);

        $reflection = new ReflectionClass ($this);
        if($reflection->implementsInterface('Data_Row_LocationInterface')){
        	$return[] = $this->_getLocationCacheId();
        }

		return $return;
    }

    protected function _getLocationCacheId()
    {
    	$cacheId = 'locationFor_'.$this->getItemType().$this->getId();
    	return $cacheId;
    }

    /**
     * Returns the cache id for the comments attached to the current user
     *
     * @return unknown
     */
    protected function _getCommentsCacheId()
    {
    	$cacheId = 'commentsFor_'.$this->getItemType().$this->getId();
    	return $cacheId;
    }

    /**
     * Return the type of this object
     *
     * @return unknown
     */
    public function getItemType()
    {
        return $this->_table->getItemType();
    }

    /**
     * Retrieves the id of the row of this item in the items table
     *
     * @return int
     */
    public function getItemId()
    {
        $table = new Item();
        $itemType = $this->getItemType();

        $result = $table->fetchRow("itemId = $this->id AND itemType = '$itemType'");
        if(empty($result)){
            return null;
        }
        return $result->id;
    }

    /**
     * Indicates the language this item is presented in
     *
     * @return string
     */
    public function getLang()
    {
    	return $this->_lang;
    }

    /**
     * Sets the flag for the language this item is presented in
     *
     * @param string $lang
     */
    protected function _setLang($lang)
    {
    	$this->_lang = $lang;
    }

	/**
     * Returns the url for the page describing the current object
     *
     * @return string
     */
    public function getLink()
    {
        $params = array(
            'name' => $this->getCleanTitle(),
            'dataType' => $this->_routeDataType,
            'id' => $this->id,
        );
        $link = Globals::getRouter()->assemble($params, $this->_route, true);
        return $link;
    }

    /**
     * Returns the url for the edition page of the current object
     *
     * @return string
     */
    public function getEditLink()
    {
        if(empty($this->id)){
            return $this->getCreateLink();
        }

        $params = array(
            'name' => Utils::cleanStringForUrl($this->getTitle()),
            'dataType' => $this->_routeDataType,
            'id' => $this->id,
        );
        $link = Globals::getRouter()->assemble($params, $this->_editRoute, true);
        return $link;
    }

    /**
     * Returns the url for the submission of a new object
     *
     * @return string
     */
    public function getCreateLink()
    {
        $link = Globals::getRouter()->assemble(array('dataType' => $this->_routeDataType), $this->_createRoute, true);
        return $link;
    }

    /**
     * Returns the url for deleting an object
     *
     * @return string
     */
    public function getDeleteLink()
    {
        $params = array(
            'name' => Utils::cleanStringForUrl($this->getTitle()),
            'dataType' => $this->_routeDataType,
            'id' => $this->id,
        );
        $link = Globals::getRouter()->assemble($params, $this->_deleteRoute, true);
        return $link;
    }

    /**
     * Returns the url for listing objects of this kind
     *
     * @return string
     */
    public function getListLink(array $params = array())
    {
        $link = Globals::getRouter()->assemble($params, $this->_listRoute, true);
        return $link;
    }

    /**
     * Returns the title, from this table or another one
     * Returns the item type in case some bug caused the title
     * to be empty.
     *
     * @return string
     */
    public function getTitle()
    {
       	$title = $this->{$this->_titleColumn};
       	if(empty($title)){
       		$title = $this->getItemType();
       	}
        return $title;
    }

    /**
     * Returns the description, from this table or another one
     *
     * @return string
     */
    public function getDescription()
    {
       	$description = $this->description;
        return $description;
    }

    /**
     * Return cleaned up (url-safe) title of the current object
     *
     * @return string
     */
    public function getCleanTitle()
    {
        $title = Utils::cleanStringForUrl($this->getTitle());
        return $title;
    }

    /**
     * Return date of submission
     *
     * @return unknown
     */
    public function getDate($useTime = true)
    {
        if(empty($this->date)){
            return null;
        }
        $date = Lib_Date::getFormattedDate($this->date, $useTime);
        return $date;
    }

    /**
     * Return the poster of the current object
     *
     * @return User_Row
     */
    public function getSubmitter()
    {
        if(empty($this->submitter)){
            return null;
        }
        $submitter = $this->findParentRow('User','Submitter');
        return $submitter;
    }

    /**
     * Return the last editor of the current object
     *
     * @return User_Row
     */
    public function getLastEditor()
    {
        if(empty($this->lastEditor)){
            return null;
        }
        $lastEditor = $this->findParentRow('User','LastEditor');
        return $lastEditor;
    }

    /**
     * Return the last edition date of the current object
     *
     * @return string
     */
    public function getLastEditionDate()
    {
        if(empty($this->lastEditionDate) ||$this->lastEditionDate == '0000-00-00 00:00:00'){
            return null;
        }
        $lastEditionDate = Lib_Date::getFormattedDate($this->lastEditionDate);
        return $lastEditionDate;
    }

    /**
     * Set tags associated to this object
     *
     * @param array $array
     */
    public function setTags($tags)
    {
        if(!is_array($tags)){
            $tags = str_replace(' ', ',', $tags);
            $tags = explode(',', $tags);
        }

        $filtered = array();

        foreach($tags as $word){

            $word = Utils::cleanString($word);
            if(strlen($word) < MIN_TAG_LENGTH){
                continue;
            }

            $filtered[] = $word;
        }

        $this->_arrTags = $filtered;
    }

    /**
     * Return tags associated to this object
     *
     * @return int
     */
    public function getTags()
    {
    	if(!is_array($this->_arrTags)){
    		if(ALLOW_CACHE){
	    		$cacheId = $this->_getTagsCacheId();
				$cache = $this->getCache();
				$this->_arrTags = $cache->load($cacheId);
    		} else {
    			$this->_arrTags = false;
    		}
			$table = new Tag();
		    if($this->_arrTags === false){
		        $select = $table->select()
					->where('itemId = ?', $this->id)
					->where('itemType = ?', $this->_table->getItemType());
	            $result = $table->fetchAll($select);
	            $this->_arrTags = array();
	            foreach($result as $row){
	                $this->_arrTags[] = $row['text'];
	            }
			}
			if(ALLOW_CACHE){
	           	$this->getTable()->saveDataInCache($cache, $this->_arrTags, $cacheId);
			}
    	}

        return $this->_arrTags;
    }

    /**
     * Returns the cache id for all tags attached to this
     * object.
     *
     * @return string
     */
    protected function _getTagsCacheId()
    {
    	$cacheId = 'tagsFor_'.$this->getItemType().$this->getId();
    	return $cacheId;
    }

    /**
     * Getter function for the category
     *
     * @return int
     */
    public function getCategory($type = null)
    {
        switch($type){
        	case 'creation':
        		$return = $this->_creationCategory;
        		break;
        	case 'display':
        	default:
        		$return = $this->_category;
        		break;
        }
        return $return;
    }

    /**
     * Getter function for the subcategory
     *
     * @return string
     */
    public function getSubCategory($type = null)
	{
        switch($type){
        	case 'creation':
        		$return = $this->_creationSubCategory;
        		break;
        	case 'display':
        	default:
        		$return = $this->_subCategory;
        		break;
        }
        return $return;
	}

    /**
     * Instantiates the form to edit this
     *
     * @param User_Row $user
     * @param Lib_Acl $acl
     * @param array $options
     * @return Data_Form
     */
    public function getForm(User_Row $user, Lib_Acl $acl, $options = null)
    {
        $form = new $this->_formClass($this, $user, $acl, $options);
        $form->setName($this->getItemType().'Form');
        return $form;
    }

	public function isReadableBy(User_Row $user, Lib_Acl $acl)
	{
		if(in_array($user->status, array(User::STATUS_ADMIN, User::STATUS_EDITOR))){
			return true;
		}

		if($this->status == Data::VALID){
			$resource = $this->_getReadResourceId();
		} else {
			$resource = Lib_Acl::PUBLIC_READ_RESOURCE.'_'.$this->submitter;
		}

		if(!$acl->has($resource)){
		    // Users do not know other users's own resource ids
		    return false;
		}

		$status = $acl->isAllowed($user->getRoleId(), $resource);
        if($status){
            return true;
        }

		$status = $acl->isAllowed($user->getOwnerRole(), $resource);
		return $status;
	}

	public function isEditableBy(User_Row $user, Lib_Acl $acl)
	{
		if(in_array($user->status, array(User::STATUS_ADMIN, User::STATUS_EDITOR))){
			return true;
		}

		$resource = $this->_getEditionResourceId();
		if(!$acl->has($resource)){
			return false;
		}
		$status = $acl->isAllowed($user->getOwnerRole(), $resource);
		return $status;
	}

	public function isCreatableBy(User_Row $user, Lib_Acl $acl)
	{
		if(in_array($user->status, array(User::STATUS_ADMIN, User::STATUS_EDITOR))){
			return true;
		}

		$resource = $this->_getCreationResourceId();
		if(!$acl->has($resource)){
			return false;
		}
		$status = $acl->isAllowed($user->getRoleId(), $resource);
		return $status;
	}

	public function isDeletableBy(User_Row $user, Lib_Acl $acl)
	{
		if(in_array($user->status, array(User::STATUS_ADMIN, User::STATUS_EDITOR))){
			return true;
		}

		$resource = $this->_getDeletionResourceId();
		if(!$acl->has($resource)){
			return false;
		}
		$status = $acl->isAllowed($user->getOwnerRole(), $resource);
		return $status;
	}

	/**
	 * This function deletes the current object and then
	 * redirects the user to a page , via 'deletedone'.
	 *
	 * @param array $params
	 */
	public function deleteAndRedirect($params = array(), User_Row $user)
	{
		$redirectUrl = $this->_getDeleteRedirectUrl($params, $user);
		if($redirectUrl[0] === '/'){
			$redirectUrl = substr($redirectUrl, 1);
		}

		if(!empty($redirectUrl)){
			$url = Globals::getRouter()->assemble(array('redirectUrl' => $redirectUrl), 'deletedatadone', true, false);
		} else {
			$url = Globals::getRouter()->assemble(array(), 'defaults', true);
		}
		$this->delete();

        $response = new Zend_Controller_Response_Http();
		$response->setRedirect($url)
                 ->sendResponse();
        exit();
	}

	/**
	 * Returns the url that the user will be redirected to
     * upon deletion of this object
	 *
	 * @return string
	 */
	protected function _getDeleteRedirectUrl($params, User_Row $user)
	{
		$itemType = $this->getItemType();
		$route = Data::getParentRouteForDataType($itemType);
		if(empty($route)){
			$route = $this->_defaultDeleteRedirectRoute;
		}
		$url = Globals::getRouter()->assemble($params, $route, true);
		return $url;
	}

	/**
     * Returns the resource for reading this object
     *
     * @return string
     */
    protected function _getReadResourceId()
    {
        $string = Lib_Acl::PUBLIC_READ_RESOURCE;
        return $string;
    }

    /**
     * Returns the resource for editing this object
     *
     * @return string
     */
    protected function _getEditionResourceId()
    {
        $string = Lib_Acl::PUBLIC_EDIT_RESOURCE . '_'.$this->submitter;
        return $string;
    }

	/**
     * Returns the resource for deleting this object
     *
     * @return string
     */
    protected function _getDeletionResourceId()
    {
        $string = Lib_Acl::PUBLIC_EDIT_RESOURCE . '_'.$this->submitter;
        return $string;
    }

    /**
     * Returns the resource for creating a new instance of this class
     *
     * @return string
     */
    protected function _getCreationResourceId()
    {
        $string = Lib_Acl::REGISTERED_RESOURCE;
        return $string;
    }

    /**
     * Inserts or updates a row
     * @param boolean $switch.
     */
    public function save($switch = false)
    {
        /**
         * $switch serves a different purpose depending
         * on whether we're doing an insert or an update
         * true for insert means: set Invalid on insert
         * true for update means: do not update automatic fields
         */
    	if (!isset($this->_cleanData['id'])) {
            return $this->_doInsert($switch);
        } else {
            return $this->_doUpdate($switch);
        }
    }

    /**
     * Whether this object is fully saved with one form (true),
     * or if we must go through two passes (false).
     *
     * @var boolean
     */
    public function onePassSubmit()
    {
    	return $this->_onePassSubmit;
    }

    public function setOnePassSubmit($boolean = true)
    {
    	$this->_onePassSubmit = ($boolean ? true : false);
    }

	/**
     * Determines what kind of editing a form must
     * be doing.
     *
     * @return string
     */
    public function getEditType()
    {
        if(empty($this->id)){
        	$return = Data::EDITTYPE_FIRST_SAVE;
        } else {
        	if(empty($this->date)){
        		$return = Data::EDITTYPE_NEXT_SAVE;
        	} else {
        		$return = Data::EDITTYPE_EDIT;
        	}
        }

        return $return;
    }

	/**
	 * Returns the whole path for the folder associated
	 * to this object
	 */
    abstract public function getFolderPath();

    /**
     * Returns the folder associated to this object;
     *
     * @return Folder
     */
    public function getFolder()
    {
    	$folderPath = $this->getFolderPath();
    	if(empty($folderPath)){
    		$class = __CLASS__;
    		$id = $this->id;
    		throw new Lib_Exception("No folder found for this object: '$class', id: '$id'");
    	}
    	$folder = new Folder($folderPath);
    	return $folder;
    }

    /**
     * Returns the name of the view layout for the given action
     *
     * @return string
     */
    public function getLayout($action = 'display')
    {
    	if(!in_array($action, Data::$actions)){
    		throw new Lib_Exception("Unknown action '$action' for getLayout");
    	}

    	if(!isset($this->_layouts[$action])){
    		$layout = APP_DEFAULT_LAYOUT;
    	} else {
    		$layout = $this->_layouts[$action];
    	}

    	return $layout;
    }

    /**
     * Creates the folder associated to this object
     *
     * @return boolean
     */
    public function createFolder()
	{
		$folderPath = $this->getFolderPath();
		$folder = Folder::create($folderPath);
		$status = !empty($folder);
		return $status;
	}

    /**
     * Deletes the folder associated to this object
     *
     * @return boolean
     */
	public function deleteFolder()
	{
		$folderPath = $this->getFolderPath();
		$folder = new Folder($folderPath);
		$status = $folder->delete();
		if(!$status){
			Globals::getLogger()->error("Could not delete folder '$folderPath'", Zend_Log::INFO);
		}
		return $status;
	}

    public function getFolderName()
	{
        $folderName = $this->getCleanTitle() . '_' . $this->id;
        return $folderName;
	}

	public function getAlbum()
	{
		if($this->_albumType == Media_Album::TYPE_AGGREGATE){
			throw new Lib_Exception('A data row with an aggregated album cannot be asked for its album via getAlbum. Use static methods in Media_Album instead');
		}

		$album = Media_Album_Factory::buildSimpleAlbumForItem($this->getItemType(), $this->getId());

		return $album;
	}

	/**
	 * Increments the view counter (when necessary)
	 * This does not deal with cache invalidation, because that
	 * task will be taken care of by an asynchronous script.
	 *
	 * @param User_Row $viewer
	 */
	public function viewBy(User_Row $viewer, Zend_Controller_Request_Http $request)
	{
		if($this->submitter == $viewer->{User::COLUMN_USERID}){
			// Do not increment view counter if the viewer is the submitter
			return;
		}

		$prefetchHeader = $request->getHeader('X-moz');
		if($prefetchHeader == 'prefetch'){
			// Do not increment view counter in case of prefetch request
			return;
		}

		$table = Constants_TableNames::ITEM_VIEW;
		$itemType = $this->getItemType();
		$itemId = $this->getId();
		$sql = "UPDATE $table SET lastView=NOW(), views = views + 1 WHERE itemType = '$itemType' AND itemId = $itemId";
		$db = $this->getTable()->getAdapter();
		$stmt = $db->query($sql);
		$count = $stmt->rowCount();
		if($count > 0){
			return;
		}

		// No update was done, let's insert a row
		$sql = "INSERT INTO $table SET lastView=NOW(), views = 1, itemType = '$itemType', itemId = $itemId";
		$db->query($sql);
	}

	/**
	 * Return the number of times this object was displayed
	 * @return int
	 */
	public function getViews()
	{
		$views = false;

		if(ALLOW_CACHE){
			$cacheId = $this->_getViewsCacheId();
			$cache = $this->getCache();
			$views = $cache->load($cacheId);
		}
		if($views === false){
			// No cache entry, let's create one
			$table = new Item_View();
			$itemType = $this->getItemType();
			$itemId = $this->getId();
			$viewsRow = $table->fetchRow("itemType = '$itemType' AND itemId = $itemId");
			if(!is_null($viewsRow)){
				$views = $viewsRow->views;
			} else {
				$views = 0;
			}
			if(ALLOW_CACHE){
				$cache->save($views, $cacheId);
			}
		}

		if(is_null($views)){
			$views = 0;
		}
		return $views;
	}

	/**
	 * Returns a string representing the content.
	 * Used for search exceprt generation.
	 */
	public function getFlatContent()
	{
		$return = ucfirst($this->getTitle());
		$return .= ' '.implode(' ', $this->getTags());
		return $return;
	}

	public function getCreateRoute()
	{
		return $this->_createRoute;
	}

	protected function _getViewsCacheId()
	{
		$id = 'viewsFor'.ucfirst($this->getItemType()).$this->getId();
		return $id;
	}

	/**
     * Special operations to be performed when inserting this object in database
     * @throws Lib_Exception_Database
     */
    protected function _doInsert($setInValid = false)
    {
        $reflection = new ReflectionClass ($this);
        if($reflection->implementsInterface('Data_Row_LocationInterface')){
            if($this->_location){
            	$this->_location->itemId = $this->getId();
                $this->_location->itemType = $this->getItemType();
            	$locationId = $this->_location->save();
                if(!$locationId){
                    throw new Lib_Exception_Database('Saving location failed (1)');
                }
            }
        }

        // Item creation
        $itemTable = new Item();
        $item = $itemTable->fetchNew();

        if($this->onePassSubmit()){
        	if(empty($this->date)){
				$this->date = date("Y-m-d H:i:s");
        	}
			$this->status = ($setInValid) ? Data::INVALID : Data::VALID;
        	$item->date = $this->date;
        } else {
        	/**
        	 * First pass of saving: we do not save date, and we set the object invalid
        	 */
        	$this->status = Data::INVALID;
        }

        if(empty($this->submitter)){
        	$this->submitter = Globals::getUser()->{User::COLUMN_USERID};
        }
        $return = parent::_doInsert();
        $this->_saveTranslatedTexts();

        $item->itemId = $this->id;
        $item->itemType = $this->_table->getItemType();
        $item->status = $this->status;
        $item->notification = $this->_notification;
        $item->submitter = $this->submitter;
        if($reflection->implementsInterface('Data_Row_MetaDataInterface')){
            $parentItem = $this->getParentItem();
            if(empty($parentItem)){
				$message = "Saving new metadata item of dataType '{$item->itemType}', id '$item->itemId'. Parent could not be found";
				Globals::getLogger()->warning($message, Zend_Log::INFO);
            } else {
           		$item->parentItemId = $parentItem->id;
            }
        }
        $item->save();

        // Album creation
        if($this->_createAlbumOnSave){
        	if($this->_albumType == Media_Album::TYPE_AGGREGATE){
        		Media_Album::createAggregateAlbumFor($this);
        	} else {
        		Media_Album::createSimpleAlbumFor($this, $this->_albumAccess);
        	}
        }

        // Folder creation
        if($this->_createFolderOnSave){
        	$this->createFolder();
        }

        if(count($this->_arrTags)){
            $this->_insertTags();
        }

        return $return;
    }

    /**
     * Special operations to be performed when updating this object in database
     *
     * @param boolean $skipAutomaticEditionFields Admins can change these fields, so
     * they need to override their automatic update.
     */
    protected function _doUpdate($skipAutomaticEditionFields = false)
    {
        $reflection = new ReflectionClass ($this);
        if($reflection->implementsInterface('Data_Row_LocationInterface')){
            if($this->_location){
                // Location not null: save its id
                $this->_location->itemId = $this->getId();
                $this->_location->itemType = $this->getItemType();
                $locationId = $this->_location->save();
                if(!$locationId){
                    throw new Lib_Exception_Database('Saving location failed (2)');
                }
            } else {
                // Location null: delete its id if necessary
                $this->location = null;
            }
        }

        $editType = $this->getEditType();
        if(!$this->onePassSubmit() && $editType == Data::EDITTYPE_NEXT_SAVE && isset($this->date)){
        	/**
        	 * We must save the date attribute, not the 'last edition date' attribute
        	 */
        	$this->date = date("Y-m-d H:i:s");
        } else {
	        if(!$skipAutomaticEditionFields){
	            $this->lastEditor = Globals::getUser()->{User::COLUMN_USERID};
	            $this->lastEditionDate = date("Y-m-d H:i:s");
	        }
        }

        $itemTable = new Item();
        $item = $itemTable->fetchRow("itemType = '".$this->_table->getItemType()."' AND itemId = $this->id");
        if($item){
            $item->status = $this->status;
            $item->notification = $this->_notification;
            if(isset($this->date)){
            	$item->date = $this->date;
            }
            $item->save();
        }
        $return = parent::_doUpdate();
        $this->_saveTranslatedTexts();
        $this->_updateTags();
        /**
         * @todo: ajouter le renommage du repertoire si un attribut le specifie
         */

        return $return;
    }

    /**
     * Insert tags associated to this object in database
     *
     */
    protected function _insertTags()
    {
        $tagsTable = new Tag();
        foreach($this->_arrTags as $tag){
            $row = $tagsTable->createRow();
            $row->itemId = $this->id;
            $row->itemType = $this->_table->getItemType();
            $row->text = $tag;
            $row->save();
        }
    }

    /**
     * Update tags associated to this object in database
     *
     */
    protected function _updateTags()
    {
        $tagsTable = new Tag();
        $where = array("itemId = $this->id", "itemType = '".$this->_table->getItemType()."'");
        $tagsTable->delete($where);

        if(count($this->_arrTags)){
            $this->_insertTags();
        }
    }

    /**
     * Delete tag, item, and translated text entries for this data.
     *
     * @return void
     */
    protected function _postDelete()
	{
		$itemType = $this->_table->getItemType();

		$where = "itemType = '$itemType' AND itemId = $this->id";

		$tagsTable = new Tag();
		$tagsTable->delete($where);

		$itemTable = new Item();
		$itemTable->delete($where);

		$viewsTable = new Item_View();
		$viewsTable->delete($where);

		$table = new Data_TranslatedText();
		$where = "id = $this->id AND itemType='$itemType' and type IN('".Data_Form_Element::TITLE."', '".Data_Form_Element::DESCRIPTION."')";
		$table->delete($where);

        // Album deletion
        if($this->_createAlbumOnSave){
        	if($this->_albumType == Media_Album::TYPE_AGGREGATE){
        		Media_Album::deleteAggregateAlbumFor($this);
        	}
        }

		parent::_postDelete();

		Globals::getLogger()->deletes("Deleted data '$this->id' of type '$itemType'", Zend_Log::INFO);
	}

	/**
	 * Determines whether a field is translated or not
	 *
	 * @param string $columnName
	 * @return boolean
	 */
	protected function _isTranslated($columnName)
	{
		$isTranslated = false ||
			($columnName == Data_Form_Element::TITLE && $this->_isTitleTranslated) ||
			($columnName == Data_Form_Element::DESCRIPTION && $this->_isDescriptionTranslated);
		return $isTranslated;
	}

}