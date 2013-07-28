<?php
/**
 * This form decorator adds map functionalities
 *
 */
class Lib_Form_Decorator_JsMap extends Zend_Form_Decorator_Abstract
{
	/**
	 * The form object this decorator is attached to
	 *
	 * @var Lib_Form
	 */
    protected $_form;
	/**
	 * The Data_Row object we are manipulating with the form
	 *
	 * @var Data_Row
	 */
    protected $_data;
	/**
	 * Id of the Dpt overlay to apply on the map, if we are submitting
	 * data for a given dpt
	 *
	 * @var int
	 */
    protected $_overlayDpt = null;
	/**
	 * Location of the dpt's center, if we are submitting
	 * data for a given dpt
	 *
	 * @var Location_Row
	 */
	protected $_dptCenterLocation = false;

	/**
	 * Apply this decoration
	 *
	 * @param string $content
	 * @return string
	 */
	public function render($content)
    {
        $this->_form = $this->getElement();
        $this->_data = $this->_form->getData();
        $view = $this->_form->getView();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

        // Remote and local scripts
//$view->JQuery()->addJavascriptFile($baseUrl . '/'.SCRIPTS_PATH.'mymap.js');

        $longitude = $this->_form->getElement('longitude');
        $latitude = $this->_form->getElement('latitude');
        if(empty($longitude) || empty($latitude)){
        	return $content;
        }
        
        // Data for the onDocumentReady script
        $hasMarker = ((strlen($longitude->getValue()) > 0) && strlen($latitude->getValue()));

        $bounds = method_exists($this->_form, 'getBounds') ? $this->_form->getBounds() : null; 
        $view->googleMaps()->editItem($hasMarker, $this->_getLocation(), $bounds);

		return $content;
    }

    /**
     * Determines overlay and dpt location
     * in case user is submitting new data
     * for a given dpt.
     */
    protected function _manageDptLocation()
    {
        if(!empty($this->_data->id)){
            return;
        }

        if(!$dptElement = $this->_form->getElement('dpt')){
            return;
        }

        // New data for a given dpt: overlay must be set in place
        $dptId = $this->_overlayDpt = $dptElement->getValue();

        // Determine new location center: dpt location
        $table = new Dpt();
        $dpt = $table->find($dptId)->current();
        if(!$dpt){
            return;
        }

        $location = $dpt->findParentRow('Location');
        if($location){
            $this->_dptCenterLocation = $location;
        }
    }

    /**
     * Returns the location to display
     *
     * @return Location_Row
     */
    protected function _getLocation()
    {
        $this->_manageDptLocation();
        if($this->_dptCenterLocation){
            // A new data is being entered for a given dpt: override default location
            return $this->_dptCenterLocation;
        }

        $table = new Location();
        $location = $table->fetchNew();

        $latitude = $this->_form->getElement('latitude')->getValue();
        $location->latitude = empty($latitude) ? DEFAULT_LATITUDE : $latitude;

        $longitude = $this->_form->getElement('longitude')->getValue();
        $location->longitude = empty($longitude) ? DEFAULT_LONGITUDE : $longitude;

        $zoom = $this->_form->getElement('zoom')->getValue();
        $location->zoom = empty($zoom) ? DEFAULT_ZOOM : $zoom;

        $yaw = $this->_form->getElement('yaw')->getValue();
        $location->yaw = empty($yaw) ? 0 : $yaw;

        $pitch = $this->_form->getElement('pitch')->getValue();
        $location->pitch = empty($pitch) ? 0 : $pitch;

        $mapType = $this->_form->getElement('mapType')->getValue();
        $location->mapType = empty($mapType) ? 0 :$mapType;

        return $location;
    }
}
