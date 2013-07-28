<?php
abstract class Media_Item_Form extends Data_Form
{
	/**
	 * Disabled because it is buggy: albums cannot be found on submit
	 * @todo: FIX IT !!!!!
	 */
	const ALLOW_ALBUM_CHANGE = false;

	/**
	 * Whether the media is required or not on form submit
	 *
	 * @var boolean
	 */
	protected $_mediaRequired = true;

	/**
	 * Constructor
	 *
	 * @param Data_Row $object
	 * @param User_Row $user
	 * @param Lib_Acl $acl
	 * @param array $options
	 */
	public function __construct(Data_Row $object, User_Row $user, Lib_Acl $acl, $options = null)
	{
		if($object->id){
			$this->_mediaRequired = false;
		}
		parent::__construct($object, $user, $acl, $options);
		$this->setAction($this->_action);
	}

	protected function _setup()
	{
		$elements = $this->_buildElements();

        if(!empty($this->_object->id) && $this->_object->hasLocation()){
        	$elements['locationFlag']->setValue(1);
        } else {
        	$elements['locationFlag']->setValue(0);
        }

        $isAllowedToEditAll = $this->_acl->isAllowed($this->_user, Lib_Acl::PUBLIC_EDIT_RESOURCE);
        $isAllowedToEditThis = $this->_acl->isAllowed(Lib_Acl::OWNER_ROLE.'_'.$this->_user->{User::COLUMN_USERID}, Lib_Acl::PUBLIC_EDIT_RESOURCE.'_'.$this->_user{User::COLUMN_USERID});
        $isAllowedToEdit = $isAllowedToEditThis || $isAllowedToEditAll;

		$isEditor = $this->_acl->isAllowed($this->_user, Lib_Acl::EDITOR_RESOURCE);
        $isAdmin = $this->_acl->isAllowed($this->_user, Lib_Acl::ADMIN_RESOURCE);

        if(!empty($this->_object->id)){
            // New post: we can decide to keep this hidden by specifying 'invalid' on submit
            $elements['status'] = $this->getStatus();
        }

        if($isAdmin && !empty($this->_object->id)){
            $adminElements = array(
                'skipAutoFields' => $this->getSkipAutoFields(),
                'submitter' => $this->getSubmitter(),
                'date' => $this->getDate(),
                'lastEditionDate' => $this->getLastEditionDate(),
                'lastEditor' => $this->getLastEditor(),
            );
            $elements = array_merge($elements, $adminElements);
        }

        $this->addElements($elements);
		$this->_setupDisplayGroups($isAdmin, $isEditor, $isAllowedToEdit);
        $this->addElements(array($this->getSubmit()));
	}

	/**
	 * Builds and returns an array of form elements
	 *
	 * @return array
	 */
	protected function _buildElements()
	{
		$isEditor = $this->_acl->isAllowed($this->_user, Lib_Acl::EDITOR_RESOURCE);

	    $elements = array(
			'media' => $this->getMedia($this->_mediaRequired),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
			'author' => $this->getAuthor(),
			'riders' => $this->getRiders(),
			'trick' => $this->getTrick(),
			'spot' => $this->getSpot(),
	    	'locationFlag' => $this->getLocationFlag(),
            'longitude' => $this->getLongitude(),
            'latitude' => $this->getLatitude(),
            'zoom' => $this->getZoom(),
            'yaw' => $this->getYaw(),
            'pitch' => $this->getPitch(),
            'mapType' => $this->getMapType(),
			'tags' => $this->getTags()
        );
        if($isEditor && $this->_object->id && self::ALLOW_ALBUM_CHANGE){
        	// Editors can move a media to a different album
        	$elements[] = $this->getAlbumId();
        }
        return $elements;
	}

	/**
	 * Sets up elements in display groups
	 *
	 * @param unknown_type $isAdmin
	 * @param unknown_type $isAllowedToEdit
	 */
	protected function _setupDisplayGroups($isAdmin, $isEditor, $isAllowedToEdit)
	{
        if($isEditor && $this->_object->id && self::ALLOW_ALBUM_CHANGE){
            $this->addDisplayGroup(array('albumId'), 'albumGroup');
        }
		$this->addDisplayGroup(array('media','title', 'description', 'author', 'riders', 'trick', 'spot'), 'mediaGroup');
        $this->addDisplayGroup(array('locationFlag', 'longitude', 'latitude', 'zoom', 'yaw', 'pitch','mapType'), 'locationGroup');
        
        if($isAdmin && !empty($this->_object->id)){
			$this->addDisplayGroup(array('tags', 'status'), 'miscGroup');
            $this->addDisplayGroup(array('skipAutoFields', 'submitter', 'date', 'lastEditionDate',  'lastEditor'), 'autoFieldsGroup');
        } elseif($isAllowedToEdit && !empty($this->_object->id)){
        	$this->addDisplayGroup(array('tags', 'status'), 'miscGroup');
        } else {
        	$this->addDisplayGroup(array('tags'), 'miscGroup');	
        }
	}

    /**
     * Factory for the author element
     *
     * @return Lib_Form_Element_Person
     */
    public function getAuthor()
    {
        $element = new Lib_Form_Element_Username('author', true);
        $element->setLabel(ucfirst(Globals::getTranslate()->_('author')))
                ->addValidator('NotEmpty');
        return $element;
    }

    /**
     * Factory for the trick element
     *
     * @return Lib_Form_Element_Trick
     */
    public function getTrick()
    {
        $element = new Lib_Form_Element_Trick('trick', true);
        $element->setLabel(ucfirst(Globals::getTranslate()->_('trick')));
        return $element;
    }

    /**
     * Factory for the spot element
     *
     * @return Lib_Form_Element_Spot
     */
    public function getSpot()
    {
        $element = new Lib_Form_Element_Spot('spot', true);
        $element->setLabel(ucfirst(Globals::getTranslate()->_('spot')));
        return $element;
    }

    /**
     * Factory for longitude element
     *
     * @return Lib_Form_Element_Location_Angle_Longitude
     */
    public static function getLongitude()
    {
        $element = new Lib_Form_Element_Location_Angle_Longitude();
        return $element;
    }

    /**
     * Factory for latitude element
     *
     * @return Lib_Form_Element_Location_Angle_Latitude
     */
    public static function getLatitude()
    {
        $element = new Lib_Form_Element_Location_Angle_Latitude();
        return $element;
    }

    /**
     * Factory for zoom element
     *
     * @return Lib_Form_Element_Location_Zoom
     */
    public static function getZoom()
    {
        $element = new Lib_Form_Element_Location_Zoom();
        return $element;
    }

    /**
     * Factory for map type element
     *
     * @return Lib_Form_Element_Location_MapType
     */
    public static function getMapType()
    {
        $element = new Lib_Form_Element_Location_MapType();
        return $element;
    }

    /**
     * Factory for yaw element
     *
     * @return Lib_Form_Element_Location_Angle_Yaw
     */
    public static function getYaw()
    {
        $element = new Lib_Form_Element_Location_Angle_Yaw();
        return $element;
    }

    /**
     * Factory for pitch element
     *
     * @return Lib_Form_Element_Location_Angle_Pitch
     */
    public static function getPitch()
    {
        $element = new Lib_Form_Element_Location_Angle_Pitch();
        return $element;
    }

    public function getLocationFlag()
	{
		$element = new Lib_Form_Element_Location_Flag(false);
		return $element;
	} 

	/**
     * Factory for the riders element
     *
     * @return Lib_Form_Element_Riders
     */
    public function getRiders()
    {
        $element = new Lib_Form_Element_Riders('riders', $this->_object);
        $element->setLabel(ucfirst(Globals::getTranslate()->_('riders')))
                ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
                ->addFilter('HTMLPurifier');
        return $element;
    }

    /**
     * Factory for the album element
     *
     * @return Lib_Form_Element_Album
     */
    public function getAlbumId()
	{
        $element = new Lib_Form_Element_Album('albumId', true, true, true);
        $element->setLabel(ucfirst(Globals::getTranslate()->_('album')))
                ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
        		->addFilter('HTMLPurifier');
        return $element;
	}

	abstract public function getMedia($required = true);

}