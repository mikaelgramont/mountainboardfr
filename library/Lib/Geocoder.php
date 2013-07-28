<?php
class Lib_Geocoder
{
	public static function getBounds($address)
	{
    	$client = new Zend_Http_Client();
    	$fullUrl = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=".urlencode($address);
		$client->setUri($fullUrl);
		$response = $client->request(Zend_Http_Client::GET);
		$json = Zend_Json_Decoder::decode($response->getBody());    		
    	if($json['status'] != 'OK'){
    		throw new Lib_Exception("Could not retrieve geo info for '".$address.'"');
    	}
    	
    	if(count($json['results']) == 0){
    		throw new Lib_Exception("No geo info for '".$address.'"');
    	}
    	
    	$result = $json['results'][0]['geometry']['viewport'];
    	
    	$bounds = implode(',', array(
    		$result['southwest']['lat'],
    		$result['southwest']['lng'],
    		$result['northeast']['lat'],
    		$result['northeast']['lng'],
    	));
    	
    	return $bounds;
	}
	
	public static function getReverseGeocoding($lat, $lon)
	{
    	$latLon = "$lat,$lon";
		$client = new Zend_Http_Client();
    	$fullUrl = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&latlng=$latLon";
		$client->setUri($fullUrl);
		$response = $client->request(Zend_Http_Client::GET);
		$json = Zend_Json_Decoder::decode($response->getBody());    		
    	if($json['status'] != 'OK'){
    		throw new Lib_Exception("Could not retrieve reverse geocoding info for '".$latLon.'"');
    	}
    	
    	if(count($json['results']) == 0){
    		throw new Lib_Exception("No reverse geocoding info for '".$latLon.'"');
    	}
    	
    	$country = $dpt = $city = null;
    	$results = array_reverse($json['results']);
    	foreach($results as $result){
			if(!$country && in_array('country', $result['types'])){
				$country = self::_countryCorrectionMap(strtolower($result['formatted_address']));
				$dptLevel = Country::getDptLevel($country);
				continue;
			}
			
			if($dptLevel && in_array($dptLevel, $result['types'])){
				foreach($result['address_components'] as $component){
					if(in_array($dptLevel, $component['types'])){
						$dpt = self::_dptCorrectionMap(strtolower($component['long_name']));
					}
				}
			}
			if(in_array('locality', $result['types'])){
				foreach($result['address_components'] as $component){
					if(in_array('locality', $component['types'])){
						$city = self::_cityCorrectionMap(strtolower($component['long_name']));
					}
				}
			}
    	}
    	
    	$msg = "lat:'$lat', lon:'$lon', country:'$country', dpt:'$dpt', city:'$city'";
    	Globals::getLogger()->geocoding($msg);
		return array($country, $dpt, $city);
	}
	
	protected static function _countryCorrectionMap($country)
	{
		switch($country){
			default:
				return $country;
		}		
	}

	protected static function _dptCorrectionMap($dpt)
	{
		switch($dpt){
			case 'savoy':
				return 'savoie';
			default:
				return $dpt;
		}		
	}
	protected static function _cityCorrectionMap($city)
	{
		switch($city){
			default:
				return $city;
		}		
	}
}