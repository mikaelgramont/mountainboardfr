<?php
class Location_Row extends Zend_Db_Table_Row_Abstract
{
	public function save()
	{
		list($countryName, $dptName, $cityName) = $this->_getReverseGeocoding();
		
		$cache = Globals::getGlobalCache();
		$countryLookup = Country::getLookupTable($cache);
		$dptLookup = Dpt::getLookupTable($cache);
		
		if($countryName && isset($countryLookup[$countryName])){
			Globals::getLogger()->geocoding("Found country '$countryName' in lookup table");
			
			$this->country = $countryLookup[$countryName];
			// Build a compound key to avoid clashes with dpt in other countries
			$dptKey = Utils::cleanStringForUrl($dptName).'-'.$countryLookup[$countryName];
			if($dptName && isset($dptLookup[$dptKey])){
				Globals::getLogger()->geocoding("Found dpt '$dptName' with key '$dptKey' in lookup table");
				$this->dpt = $dptLookup[$dptKey];
			} else {
				Globals::getLogger()->geocoding("Did not find dpt '$dptName' with key '$dptKey' in lookup table");
				$this->dpt = null;
			}
		} else {
			Globals::getLogger()->geocoding("Did not find country'$countryName' in lookup table");
			$this->dpt = $this->country = null;
		}
		
		if($cityName){
			$this->city = $cityName;
		} else {
			$this->city = null;
		}
		
		return parent::save();
	}
	
	protected function _getReverseGeocoding()
	{
		$info = Lib_Geocoder::getReverseGeocoding($this->latitude, $this->longitude);
		return $info;
	}
	
	public function getCountry()
	{
        if(empty($this->country)){
            return null;
        }
        $country = $this->findParentRow('Country');
        return $country;
	}

	public function getDpt()
	{
        if(empty($this->dpt)){
            return null;
        }
        $dpt = $this->findParentRow('Dpt');
        return $dpt;
	}
}