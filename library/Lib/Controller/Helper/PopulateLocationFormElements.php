<?php
class Lib_Controller_Helper_PopulateLocationFormElements extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Populates map-related form elements
     *
     * @param Data_Row $dataRow
     * @param array $postData
     * @return array
     */
	public function direct(Data_Row $dataRow, array $postData)
    {
        $return = array();
        if($dataRow->hasLocation()){
            $location = $dataRow->getLocation();
            if(empty($location)){
                return $return;
            }
            $return['longitude'] = $location->longitude;
            $return['latitude'] = $location->latitude;
            $return['zoom'] = $location->zoom;
            $return['yaw'] = $location->yaw;
            $return['pitch'] = $location->pitch;
            $return['mapType'] = $location->mapType;
        }

        if(array_key_exists('longitude', $postData)){
            $return['longitude'] = $postData['longitude'];
        }
        if(array_key_exists('latitude', $postData)){
            $return['latitude'] = $postData['latitude'];
        }
        if(array_key_exists('zoom', $postData)){
            $return['zoom'] = $postData['zoom'];
        }
        if(array_key_exists('yaw', $postData)){
            $return['yaw'] = $postData['yaw'];
        }
        if(array_key_exists('pitch', $postData)){
            $return['pitch'] = $postData['pitch'];
        }
        if(array_key_exists('mapType', $postData)){
            $return['mapType'] = $postData['mapType'];
        }

        return $return;
	}
}