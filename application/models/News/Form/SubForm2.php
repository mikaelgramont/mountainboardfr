<?php
class News_Form_SubForm2 extends Article_Form_SubForm2 implements Data_Form_DocumentInterface
{
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
            'author' => $this->getAuthor(),
            'content' => $this->getContent(),
            'spot' => $this->getSpot(),
        	'locationFlag' => $this->getLocationFlag(),
            'longitude' => $this->getLongitude(),
            'latitude' => $this->getLatitude(),
            'zoom' => $this->getZoom(),
            'yaw' => $this->getYaw(),
            'pitch' => $this->getPitch(),
            'mapType' => $this->getMapType(),
            'tags' => $this->getTags(),
        );
        
        if(!empty($this->_object->id) && $this->_object->hasLocation()){
        	$elements['locationFlag']->setValue(1);
        } else {
        	$elements['locationFlag']->setValue(0);
        }
        
        $this->addElements($elements);

        $this->addDisplayGroup(array('title', 'description', 'author', 'content'), 'articleGroup');
        $this->addDisplayGroup(array('spot'), 'whereGroup');
        $this->addDisplayGroup(array('locationFlag', 'longitude', 'latitude', 'zoom', 'yaw', 'pitch', 'mapType'), 'locationGroup');
        $this->addDisplayGroup(array('tags'), 'miscGroup');

        $this->addElements(array($this->getSubmit()));
    }

    /**
     * Factory for spot element
     *
     * @return Lib_Form_Element_Spot
     */
    public static function getSpot()
    {
        $element = new Lib_Form_Element_Spot('spot',true);
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
}
