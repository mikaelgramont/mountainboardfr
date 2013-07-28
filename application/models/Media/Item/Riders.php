<?php
class Media_Item_Riders extends Zend_Db_Table_Abstract
{
    protected $_name = Constants_TableNames::MEDIAITEMRIDERS;

    protected $_rowClass = 'Media_Item_Riders_Row';
    
    public function insertRiders($mediaId, array $riders)
    {
    	foreach($riders as $username => $userId){
    		if($userId){
    			$this->insert(array(
	    			'mediaId' => $mediaId,
    				'riderId' => $userId,
    			));
    		} else {
    			$this->insert(array(
	    			'mediaId' => $mediaId,
    				'riderId' => 0,
    				'riderName' => $username
    			));
    		}
    	}
    }
    
    public function updateRiders($mediaId, array $riders)
    {
		$this->delete("mediaId = $mediaId");
    	$this->insertRiders($mediaId, $riders);
    }
}