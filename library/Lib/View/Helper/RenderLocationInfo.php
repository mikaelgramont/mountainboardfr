<?php
class Lib_View_Helper_RenderLocationInfo extends Zend_View_Helper_Abstract
{
	public function renderLocationInfo($data)
	{
		if(!$data->hasLocation()){
			return '';
		}
		
		list($city, $dptRow, $countryRow) = $data->getCityDptAndCountry();
		
		if($countryRow && $dptRow && $city){
			$countryLink = $this->view->itemLink($countryRow);
			$dptLink = $this->view->itemLink($dptRow);
			$locationString = "$city, $dptLink ($countryLink)";
			
		} elseif($countryRow && $dptRow){
			$countryLink = $this->view->itemLink($countryRow);
			$dptLink = $this->view->itemLink($dptRow);
			$locationString = "$dptLink ($countryLink)";
			
		} elseif($countryRow){
			$locationString = $this->view->itemLink($countryRow);
			
		} elseif($dptRow){
			$locationString = $this->view->itemLink($dptRow);
			
		} elseif($city){
			$locationString = $city;
			
		} else {
			$locationString = '';
		}	
		
		if($locationString){
			$return = '			<span class="locationString">'. $locationString . '</span>'.PHP_EOL;
		} else {
			$return = '';
		}
		
		return $return;
	}
}