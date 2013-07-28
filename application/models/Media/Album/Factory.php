<?php
class Media_Album_Factory
{
	public static function buildAlbumById($albumId, $page = 1)
	{
		$db = Globals::getMainDatabase();
		$sql = "
			SELECT a.id, agg.keyName, agg.keyValue, mai.itemType, mai.itemId
			FROM ".Constants_TableNames::ALBUM." a
			LEFT JOIN ".Constants_TableNames::AGGREGATION." agg ON a.id = agg.albumId
			LEFT JOIN ".Constants_TableNames::ALBUM_ITEM ." mai ON a.id = mai.albumId
			WHERE a.id = $albumId
			LIMIT 1
		";
		$stmt = $db->query($sql);
		$data = $stmt->fetch();
		if(empty($data)){
			throw new Lib_Exception_User("Album '$albumId' was not found");
		}

		if(empty($data['keyName'])){
			// album simple
			$album = self::buildSimpleAlbum($albumId, $page);
		} elseif(empty($data['itemType'])) {
			$dataType = Data::mapDataType($data['keyName']);
			$table = new $dataType();
			if($dataType == ucfirst(User::ITEM_TYPE)){
				// User aggregate album
				$user = $table->find($data['keyValue'])->current();
				$album = self::buildAggregateUserAlbum($user, $page);
			} else {
				// Item aggregate album
				$album = self::buildAggregateItemAlbum($data['keyName'], $data['keyValue'], $page);
			}
		} else {
			throw new Lib_Exception_User("Could not build album '$albumId' - impossible to determine the type of album");
		}

		return $album;
	}

	public static function buildSimpleAlbumForItem($itemType, $itemId, $page = 1)
	{
		$db = Globals::getMainDatabase();
		$sql = "
			SELECT a.id
			FROM ".Constants_TableNames::ALBUM." a
			JOIN ".Constants_TableNames::ALBUM_ITEM ." mai ON a.id = mai.albumId
			WHERE mai.itemId = $itemId AND mai.itemType = '$itemType'
			LIMIT 1
		";
		$stmt = $db->query($sql);
		$data = $stmt->fetch();

		$album = self::buildSimpleAlbum($data['id'], $page);
		return $album;
	}

	public static function buildSimpleAlbum($albumId, $page = 1)
	{
		switch($albumId){
			case Media_Album_PhotoMain::ID:
				$table = new Media_Album_PhotoMain();
				break;
			case Media_Album_VideoMain::ID:
				$table = new Media_Album_VideoMain();
				break;
			case Media_Album_Portfolio::ID:
				$table = new Media_Album_Portfolio();
				break;
			default:
				$table = new Media_Album_Simple();
				break;
		}

		$album = $table->find($albumId);
		$album = $album->current();
		$album->page = $page;
		return $album;

	}

	public static function buildAggregateUserAlbum(User_Row $user, $page = 1)
	{
    	$db = Globals::getMainDatabase();
		$sql =
    		'SELECT a.*
    		FROM '.Constants_TableNames::AGGREGATION.' agg
    		JOIN '.Constants_TableNames::ALBUM." a ON a.id = agg.albumId
    		WHERE agg.keyName = 'user'
    		AND a.albumType = '".Media_Album::TYPE_AGGREGATE."'
    		AND a.albumCreation = '".Media_Album::CREATION_AUTOMATIC."'
    		AND agg.keyValue = ".$user->getId();
		$stmt = $db->query($sql);
		$data = $stmt->fetch();

		if(empty($data)){
			throw new Lib_Exception_User("No profile album exists for user '".$user->getId().'"');
		}

		$table = new Media_Album_Aggregate_User();
		$album = new Media_Album_Aggregate_User_Row(array('data' => $data, 'table' => $table, 'stored' => true, 'user' => $user, 'page' => $page));
		return $album;
	}

	public static function buildAggregateItemAlbum($itemType, $itemId, $page = 1)
	{
    	$db = Globals::getMainDatabase();
		$sql =
    		'SELECT a.* FROM '.Constants_TableNames::AGGREGATION.' agg
    		JOIN '.Constants_TableNames::ALBUM." a ON a.id = agg.albumId
    		WHERE agg.keyName = '$itemType'
    		AND agg.keyValue = ".$itemId;
		$stmt = $db->query($sql);
		$data = $stmt->fetch();

		if(empty($data)){
			throw new Lib_Exception_User("No album exists for data of type '$itemType' and id '$itemId'");
		}

		$table = new Media_Album_Aggregate_Item();
		$album = new Media_Album_Aggregate_Item_Row(array('data' => $data, 'table' => $table, 'stored' => true, 'itemType' => $itemType, 'itemId' => $itemId, 'page' => $page));
		return $album;
	}
}