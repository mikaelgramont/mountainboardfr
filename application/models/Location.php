<?php
class Location extends Cache_Object
{
    const ITEM_TYPE = 'location';

    protected $_itemType = 'location';

    protected $_rowClass = 'Location_Row';

    protected $_name = Constants_TableNames::LOCATION;

    const MAPTYPENORMAL     = 'map';
    const MAPTYPESATELLITE  = 'sat';
    const MAPTYPEHYBRID     = 'hybrid';
    const MAPTYPEPHYSICAL   = 'physical';
    const MAPTYPEEARTH      = 'earth';
    const MAPTYPESTREETVIEW = 'streetview';

    public static $mapTypes = array(
        0 => self::MAPTYPENORMAL,
        1 => self::MAPTYPESATELLITE,
        2 => self::MAPTYPEHYBRID,
        3 => self::MAPTYPEPHYSICAL,
        4 => self::MAPTYPEEARTH,
        5 => self::MAPTYPESTREETVIEW,
    );

    public static $mapTypeIds = array(
        0 => 'roadmap',
        1 => 'satellite',
        2 => 'hybrid',
        3 => 'terrain',
        4 => 'hybrid',
        5 => 'hybrid',
    );

    const STATIC_MAPTYPENORMAL    = 'roadmap';
    const STATIC_MAPTYPESATELLITE = 'satellite';
    const STATIC_MAPTYPEHYBRID    = 'hybrid';
    const STATIC_MAPTYPEPHYSICAL  = 'terrain';

    public static $staticMapTypes = array(
        0 => self::STATIC_MAPTYPENORMAL,
        1 => self::STATIC_MAPTYPESATELLITE,
        2 => self::STATIC_MAPTYPEHYBRID,
        3 => self::STATIC_MAPTYPEPHYSICAL,
        4 => self::STATIC_MAPTYPESATELLITE,
        5 => self::STATIC_MAPTYPENORMAL,
    );
    
    protected $_referenceMap    = array(
		'Country' => array(
            'columns'           => 'country',
            'refTableClass'     => 'Country',
            'refColumns'        => 'id'
        ),
        'Dpt' => array(
            'columns'           => 'dpt',
            'refTableClass'     => 'Dpt',
            'refColumns'        => 'id'
        ),
	);
}