<?php
class Spot extends Data
{
    const ITEM_TYPE = 'spot';

    protected $_itemType = 'spot';

    protected $_name = Constants_TableNames::SPOT;

    protected $_rowClass = 'Spot_Row';

    protected $_referenceMap    = array(
        'LastEditor' => array(
            'columns'           => 'last_editor',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'Submitter' => array(
            'columns'           => 'submitter',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'Dpt' => array(
            'columns'           => 'dpt',
            'refTableClass'     => 'Dpt',
            'refColumns'        => 'id'
        ),
    );

    const SPOTTYPEFREERIDE  = 'spotTypeFreeride';
    const SPOTTYPEDOWNHILL  = 'spotTypeDownhill';
    const SPOTTYPEURBAN     = 'spotTypeUrban';
    const SPOTTYPESKATEPARK = 'spotTypeSkatepark';
    const SPOTTYPECENTER    = 'spotTypeCenter';
    const SPOTTYPEDIRTMTB   = 'spotTypeDirtMTB';
    const SPOTTYPEDIRTBMX   = 'spotTypeDirtBMX';
    const SPOTTYPEBOARDERX  = 'spotTypeBoarderX';
    const SPOTTYPEOTHER     = 'spotTypeOther';

    public static $spotTypes = array(
        1 =>self::SPOTTYPEFREERIDE,
        2 =>self::SPOTTYPEDOWNHILL,
        3 =>self::SPOTTYPEURBAN,
        4 =>self::SPOTTYPESKATEPARK,
        5 =>self::SPOTTYPECENTER,
        6 =>self::SPOTTYPEDIRTMTB,
        7 =>self::SPOTTYPEDIRTBMX,
        8 =>self::SPOTTYPEBOARDERX,
        9 =>self::SPOTTYPEOTHER,
    );

    const GROUNDTYPEGRASS = 'groundTypeGrass';
    const GROUNDTYPEFOREST = 'groundTypeForest';
    const GROUNDTYPEDIRT = 'groundTypeDirt';
    const GROUNDTYPEASPHALT = 'groundTypeAsphalt';
    const GROUNDTYPEGRAVEL = 'groundTypeGravel';
    const GROUNDTYPEWOOD = 'groundTypeWood';
    const GROUNDTYPEMETAL = 'groundTypeMetal';
    const GROUNDTYPECONCRETE = 'groundTypeConcrete';
    const GROUNDTYPEOTHER = 'groundTypeOther';

    public static $groundTypes = array(
        1 =>self::GROUNDTYPEGRASS,
        2 =>self::GROUNDTYPEFOREST,
        3 =>self::GROUNDTYPEDIRT,
        4 =>self::GROUNDTYPEASPHALT,
        5 =>self::GROUNDTYPEGRAVEL,
        6 =>self::GROUNDTYPEWOOD,
        7 =>self::GROUNDTYPEMETAL,
        8 =>self::GROUNDTYPECONCRETE,
        9 =>self::GROUNDTYPEOTHER,
    );
    
    public static function getSpotsByDpt($dpt)
    {
        $table = new Spot();
        $where = $table->getAdapter()->quoteInto("dpt = ?", $dpt);
        $rowset = $table->fetchAll($where);
        $spots = array();
        foreach($rowset as $row){
        	$spots[] = $row;
		}
    	return $spots;
    }
    
    /**
     * Transition from storing the dpt number in the spots table
     * to using position.
     * @param array $allItemsInFrance
     */
    public static function getFrenchSpotsWithoutLocation($allItemsInFrance)
    {
    	$frenchSpotsWithLocation = array();
    	foreach($allItemsInFrance as $item){
    		if($item instanceof Spot_Row){
    			$frenchSpotsWithLocation[] = $item->getId();
    		}
    	}
    	$list = implode(', ', $frenchSpotsWithLocation);
    	
        $table = new Spot();
        $where = "dpt <= ".Dpt::LAST_FRENCH_DPT_ID." AND id NOT IN(".$list.")";
        $rowset = $table->fetchAll($where);
        $spots = array();
        foreach($rowset as $row){
        	$spots[] = $row;
		}
    	return $spots;
    }

    /**
     * Transition from storing the dpt number in the spots table
     * to using position.
     * @param array $allItemsInFrenchDpt
     */
    public static function getFrenchDptSpotsWithoutLocation($dpt, $allItemsInFrenchDpt)
    {
    	$frenchDptSpotsWithLocation = array();
    	foreach($allItemsInFrenchDpt as $item){
    		if($item instanceof Spot_Row){
    			$frenchDptSpotsWithLocation[] = $item->getId();
    		}
    	}
    	$list = implode(', ', $frenchDptSpotsWithLocation);
    	
        $table = new Spot();
        $where = $table->getAdapter()->quoteInto("dpt = ?", $dpt);
        if($list){
        	$where .= " AND id NOT IN(".$list.")";
        }
        $rowset = $table->fetchAll($where);
        $spots = array();
        foreach($rowset as $row){
        	$spots[] = $row;
		}
    	return $spots;
    }
    
}