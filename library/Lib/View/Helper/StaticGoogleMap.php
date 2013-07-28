<?php
class Lib_View_Helper_StaticGoogleMap
{
    /**
     * Retrieves the static google map image for the given Location_Row
     *
     * @param Location_Row $location
     * @param int $width
     * @param int $height
     * @param array $markers
     * @return string
     */
    public function staticGoogleMap(Location_Row $location = null, $width = 512, $height = 512, $markers = array())
    {
        $url = 'http://maps.google.com/staticmap';

        if(empty($location)){
            throw new Lib_Exception('location must not be empty');
        }

        if(!is_array($markers)){
            throw new Lib_Exception('markers must be an array');
        }

        // If no markers given, use the center as a marker
        if(!count($markers)){
            $markers[] = array(
                'latitude' => $location->latitude,
                'longitude' => $location->longitude
            );
        }

        $markerStrings = array();
        foreach($markers as $marker){
            $markerStrings[] = $this->_getMarkerString($marker);
        }
        $markerStrings = implode('|', $markerStrings);

        $zoom = ($location->mapType >=4) ? 17 : $location->zoom;

        $format = $url.'?center=%s,%s&amp;zoom=%d&amp;size=%dx%d&amp;maptype=%s&amp;markers=%s';
        $imageUrl = sprintf($format,
            $location->latitude,
            $location->longitude,
            $zoom,
            $width,
            $height,
            Location::$staticMapTypes[$location->mapType],
            $markerStrings
        );
        return $imageUrl;
    }

    /**
     * Builds a string describing a marker
     *
     * @param array $marker
     * @return string
     */
    protected function _getMarkerString(array $marker)
    {
        $marker['size'] = empty($marker['size']) ? '' : $marker['size'];
        $marker['color'] = empty($marker['color']) ? 'yellow' : $marker['color'];
        $marker['tag'] = empty($marker['tag']) ? '' : $marker['tag'];

        $string = urlencode("{$marker['latitude']},{$marker['longitude']},{$marker['size']}{$marker['color']}{$marker['tag']}");
        return $string;
    }
}