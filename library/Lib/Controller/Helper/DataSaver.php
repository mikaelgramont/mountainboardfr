<?php
class Lib_Controller_Helper_DataSaver extends Zend_Controller_Action_Helper_Abstract
{
    public function direct()
    {
    	return $this;
    }

    /**
     * Saves a Data_Row in database, setting its data from the submitted form
     *
     * @param Data_Row $data
     * @param Data_Form $form
     * @param User_Row $user
     * @param Lib_Acl $acl
     * @param array $disregardUpdates
     * @return int
     * @throws Lib_Exception
     */
    public function save(Data_Row $dataRow, Data_Form $form, array $data, User_Row $user, Lib_Acl $acl, array $disregardUpdates = array())
    {
    	$reflection = new ReflectionClass ($dataRow);

    	// Clearing spot album caches if the data moved to  new spot
    	if($reflection->implementsInterface('Data_Row_SpotInterface') && $dataRow->spot != $data['spot']){
    		if($spot = $dataRow->getSpot() && $spot instanceof Spot_Row){
				$oldSpotAlbumId = $spot()->getAlbum()->getId();
				$oldSpotAlbumCacheId = Media_Album::getCacheId($oldSpotAlbumId);
				$dataRow->getCache()->remove($oldSpotAlbumCacheId);
    		}

            $spotTable = new Spot();
            if(strpos($data['spot'], NOREALDATA_MARK) === false){
	            $res = $spotTable->find($data['spot']);
	            if($res){
	            	$newSpot = $res->current();
	            	if($newSpot){
	            		$newSpotAlbumId = $newSpot->getAlbum()->getId();
	            		$newSpotAlbumCacheId = Media_Album::getCacheId($newSpotAlbumId);
	            		$newSpot->getCache()->remove($newSpotAlbumCacheId);
	            	}
	            }
            }
            $spotCacheId = $dataRow->getParentRowCacheId('Spot');
            $dataRow->getCache()->remove($spotCacheId);
        }

    	// Clearing trick album caches if the data moved to  new trick
    	if($reflection->implementsInterface('Data_Row_TrickInterface') && $dataRow->trick != $data['trick']){
            if($trick = $dataRow->getTrick() && $trick instanceof Trick_Row){
				$oldTrickAlbumId = $trick->getAlbum()->getId();
				$oldTrickAlbumCacheId = Media_Album::getCacheId($oldTrickAlbumId);
				$dataRow->getCache()->remove($oldTrickAlbumCacheId);
            }

            $trickTable = new Trick();
            if(strpos($data['trick'], NOREALDATA_MARK) === false){
	            $res = $trickTable->find($data['trick']);
	            if($res){
	            	$newTrick = $res->current();
	            	if($newTrick){
	            		$newTrickAlbumId = $newTrick->getAlbum()->getId();
	            		$newTrickAlbumCacheId = Media_Album::getCacheId($newTrickAlbumId);
	            		$newTrick->getCache()->remove($newTrickAlbumCacheId);
	            	}
	            }
            }

            $trickCacheId = $dataRow->getParentRowCacheId('Trick');
            $dataRow->getCache()->remove($trickCacheId);
        }

        // Update of fields
    	foreach($data as $key => $value){
            if(in_array($key, $disregardUpdates)){
                continue;
            }
            $dataRow->$key = $value;
        }

        // Blog post
        if($dataRow instanceof Blog_Post_Row){
        	$dataRow->blogId = $user->getBlog()->id;
        }

        // Tags
        $tags = $form->getElement('tags');
        if($tags){
            $dataRow->setTags($tags->getValue());
        }

        // Saving
        $skipAutomaticEditionFields  = $acl->isAllowed($user, Lib_Acl::ADMIN_RESOURCE);
        if(array_key_exists('skipAutoFields', $data)){
        	$skipAutomaticEditionFields &= $data['skipAutoFields'];
        } else {
        	$skipAutomaticEditionFields = false;
        }
        $dataRow->save($skipAutomaticEditionFields);
        if(empty($dataRow->id)){
            throw new Lib_Exception("Could not save data");
        }

        // Location
        if($reflection->implementsInterface('Data_Row_LocationInterface')){
            $this->manageLocation($dataRow, $data);
        }


        // Cache clearing
        if($reflection->implementsInterface('Data_Row_MetaDataInterface')){
            $parentRow = $dataRow->getParentItemfromDatabase();
            $parentRow->clearCache();
        } else {
        	$dataRow->clearCache();
        }
        return $dataRow->id;
    }

    /**
     * Takes care of inserting, updating and deleting of Location
     *
     * @param Data_Row|User_Row $object
     * @param array $data
     */
    public function manageLocation($object, array $data)
    {
        $location = $object->getLocation();
        if($location && (empty($data['longitude']) || empty($data['latitude']))){
        	// Deleting an existing location
        	$location->delete();
        	return;
        }

        if(empty($data['longitude']) || empty($data['latitude'])){
        	return;
        }

        if(!$location){
			$table = new Location();
			$location = $table->fetchNew();
        }

        // Creating/updating
        $location->longitude = $data['longitude'];
		$location->latitude = $data['latitude'];
		$location->zoom = $data['zoom'];
		$location->yaw = $data['yaw'];
		$location->pitch = $data['pitch'];
		$location->mapType = $data['mapType'];
        $location->status = Data::VALID;
        $location->itemId = $object->getId();
        $location->itemType = $object->getItemType();
        $location->save();
    }
}
