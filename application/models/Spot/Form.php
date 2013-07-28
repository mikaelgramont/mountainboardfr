<?php
class Spot_Form extends Data_Form
{
    /**
     * When adding a spot in a given region,
     * we want to preset the map to display these bounds;
     * @var array
     */
	protected $_bounds = array(); 
    
	public function setBounds($bounds)
	{
		$this->_bounds = $bounds;
	}
	
	public function getBounds()
	{
		return $this->_bounds;
	}
	
	/**
     * Returns a list of form elements that match the database columns
     *
     * @return array
     */
    protected function _setup()
    {
        $elements = array(
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'spotType' => $this->getSpotType(),
            'groundType' => $this->getGroundType(),
            'difficulty' => $this->getDifficulty(),
            'locationFlag' => $this->getLocationFlag(),
            'longitude' => $this->getLongitude(),
            'latitude' => $this->getLatitude(),
            'zoom' => $this->getZoom(),
            'yaw' => $this->getYaw(),
            'pitch' => $this->getPitch(),
            'mapType' => $this->getMapType(),
            'tags' => $this->getTags(),
        );

        $isAllowedToEditAll = $this->_acl->isAllowed($this->_user, Lib_Acl::PUBLIC_EDIT_RESOURCE);
        $isAllowedToEditThis = $this->_acl->isAllowed(Lib_Acl::OWNER_ROLE.'_'.$this->_user->{User::COLUMN_USERID}, Lib_Acl::PUBLIC_EDIT_RESOURCE.'_'.$this->_user{User::COLUMN_USERID});
        $isAllowedToEdit = $isAllowedToEditThis || $isAllowedToEditAll;

        $isAdmin = $this->_acl->isAllowed($this->_user, Lib_Acl::ADMIN_RESOURCE);

        if(empty($this->_object->id)){
            // New post: we can decide to keep this hidden by specifying 'invalid' on submit
            $elements['status'] = $this->getStatus();
        } elseif($isAllowedToEdit){
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
        
        if(!empty($this->_object->id) && $this->_object->hasLocation()){
        	$elements['locationFlag']->setValue(1);
        } else {
        	$elements['locationFlag']->setValue(0);
        }

        $this->addElements($elements);

        $this->addDisplayGroup(array('title', 'description', 'spotType', 'groundType', 'difficulty'), 'spotGroup');
        $this->addDisplayGroup(array('locationFlag', 'longitude', 'latitude', 'zoom', 'yaw', 'pitch','mapType'), 'locationGroup');
        $this->addDisplayGroup(array('tags', 'status'), 'miscGroup');
        if($isAdmin && !empty($this->_object->id)){
            $this->addDisplayGroup(array('skipAutoFields', 'submitter', 'date', 'lastEditionDate',  'lastEditor'), 'autoFieldsGroup');
        }

        $this->addElements(array($this->getSubmit()));
    }

    /**
     * Factory for difficulty element
     *
     * @return Lib_Form_Element_Difficulty
     */
    protected function getDifficulty()
    {
        $element = new Lib_Form_Element_Difficulty();
        return $element;
    }

    /**
     * Factory for spot type element
     *
     * @return Lib_Form_Element_SpotType
     */
    protected function getSpotType()
    {
        $element = new Lib_Form_Element_Spot_Type();
        return $element;
    }

    /**
     * Factory for ground type element
     *
     * @return Lib_Form_Element_GroundType
     */
    protected function getGroundType()
    {
        $element = new Lib_Form_Element_Spot_GroundType();
        return $element;
    }

    /**
     * Factory for longitude element
     *
     * @return Lib_Form_Element_Location_Angle_Longitude
     */
    public function getLongitude()
    {
        $element = new Lib_Form_Element_Location_Angle_Longitude();
        return $element;
    }

    /**
     * Factory for latitude element
     *
     * @return Lib_Form_Element_Location_Angle_Latitude
     */
    public function getLatitude()
    {
        $element = new Lib_Form_Element_Location_Angle_Latitude();
        return $element;
    }

    /**
     * Factory for zoom element
     *
     * @return Lib_Form_Element_Location_Zoom
     */
    public function getZoom()
    {
        $element = new Lib_Form_Element_Location_Zoom();
        return $element;
    }

    /**
     * Factory for map type element
     *
     * @return Lib_Form_Element_Location_MapType
     */
    public function getMapType()
    {
        $element = new Lib_Form_Element_Location_MapType();
        return $element;
    }

    /**
     * Factory for yaw element
     *
     * @return Lib_Form_Element_Location_Angle_Yaw
     */
    public function getYaw()
    {
        $element = new Lib_Form_Element_Location_Angle_Yaw();
        return $element;
    }

    /**
     * Factory for pitch element
     *
     * @return Lib_Form_Element_Location_Angle_Pitch
     */
    public function getPitch()
    {
        $element = new Lib_Form_Element_Location_Angle_Pitch();
        return $element;
    }

	/**
	 * Factory. This flag allows to make locations madatory:
	 * When setting a location, the client-side JS sets the
	 * input value to 1. When erasing the location, the JS sets the
	 * input value to 0.
	 */
    public function getLocationFlag()
	{
		$element = new Lib_Form_Element_Location_Flag();
		return $element;
	} 
}