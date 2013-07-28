<?php
class Media_Item_Video_Form extends Media_Item_Form
{
	/**
	 * Builds and returns an array of form elements
	 *
	 * @return array
	 */
	protected function _buildElements()
	{
		$elements = array(
			'media' => $this->getMedia(),
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
        return $elements;
	}

	/**
	 * Sets up elements in display groups
	 *
	 * @param boolean $isAdmin
	 * @param boolean $isEditor
	 * @param boolean $isAllowedToEdit
	 */
	protected function _setupDisplayGroups($isAdmin, $isEditor, $isAllowedToEdit)
	{
		$this->addDisplayGroup(array('media', 'title', 'description', 'author', 'riders', 'trick', 'spot'), 'mediaGroup');
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
     * Factory for the video element
     *
     * @param boolean $required
     * @return Lib_Form_Element_Video
     *
     */
    public function getMedia($required = true)
    {
        $element = new Lib_Form_Element_Video($this->_object, $required, 'media');
        return $element;
    }
}