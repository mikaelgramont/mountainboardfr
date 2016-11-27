<?php
class Lib_View_Helper_GoogleMaps extends Zend_View_Helper_Abstract
{
    public function googleMaps()
    {
        return $this;
    }

    /**
     * Displays a map for a given region,
     * with markers for all items found in the page.
     * @param unknown_type $userParams
     */
    public function regionDisplay($params)
    {
    	$neededOptions = array(
    		'mapElementId','bounds'
    	);
    	$options = array();
    	foreach($neededOptions as $key){
    		$options[$key] = $params[$key];
    	}
    	$optionsJson = Zend_Json_Encoder::encode($options);

    	$this->_addJs();
    	$this->view->JQuery()->addJavascriptFile($this->view->asset()->script('jquery.tablesorter.min.js'));
    	//$this->view->JQuery()->addJavascriptFile($this->view->asset()->script('jquery.tablesorter.pager.js'));
    	$js = "	Lib.Maps.displayRegion.init($optionsJson, Lib.Maps.displayRegion.loadMarkers);".PHP_EOL;
    	$this->view->getHelper('jQuery')->addOnLoad($js);
    }

    /**
     * Displays a map to show a single item
     * @param Location_Row $location
     * @param array $params
     */
    public function displayItem(Location_Row $location, $params)
    {
    	$options = array(
    		'mapElementId' => $params['mapElementId'],
    		'center' => array(
    			$location->latitude,
    			$location->longitude
    		),
    		'zoom' => $location->zoom,
    		'mapTypeId' => Location::$mapTypeIds[$location->mapType],
		);
    	if($dpt = $location->getDpt()){
        	$options['regionDetails'] = '<div class="regionDetails">'.ucfirst($this->view->translate('moreInThisRegion')).': '.$this->view->itemLink($dpt).'</div>';
    	}

    	$optionsJson = Zend_Json_Encoder::encode($options);

    	$this->_addJs();
    	$js = "	Lib.Maps.displayItem.init($optionsJson);".PHP_EOL;
    	$this->view->getHelper('jQuery')->addOnLoad($js);
    }

    /**
     * Displays a map in a form
     * @param $hasMarker
     * @param Location_Row $location
     * @param $bounds
     */
    public function editItem($hasMarker, Location_Row $location, $bounds)
    {
    	if($bounds === null){
    		$bounds = array();
    	}

    	$options = array(
    		'mapElementId' => 'mapElement',
    		'center' => array(
    			$location->latitude,
    			$location->longitude
    		),
    		'zoom' => $location->zoom,
    		'hasMarker' => $hasMarker,
    		'bounds' => $bounds,
    		'mapTypeId' => Location::$mapTypeIds[$location->mapType],
			'clearLocation' => ucfirst($this->view->translate('clearLocation')),
			'locateMe' => ucfirst($this->view->translate('locateMe')),
    		'mapLabel' => ucfirst($this->view->translate('mapLabel')),
		);

    	$optionsJson = Zend_Json_Encoder::encode($options);

    	$this->_addJs();
    	$js = "	Lib.Maps.editItem.init($optionsJson);".PHP_EOL;
    	$this->view->getHelper('jQuery')->addOnLoad($js);
    }

    protected function _addJs()
    {
        $mapsUrl = GOOGLEMAPS_URL_NEW . '&key=' . GOOGLE_APIS_KEY;
    	$this->view->JQuery()->addJavascriptFile($mapsUrl);
    	$this->view->JQuery()->addJavascriptFile($this->view->asset()->script('libMaps.js'));
    	$this->view->JQuery()->addJavascriptFile($this->view->asset()->script('jquery.tablesorter.js'));
    }
}