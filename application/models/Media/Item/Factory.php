<?php
class Media_Item_Factory
{
	/**
	 * Builds and returns a concrete Media_Item_Row
	 *
	 * @param Media_Item_Row
	 * @return Media_Item_Row
	 */
	public static function buildItem($id, $mediaType)
	{
		switch($mediaType){
			case Media_Item::TYPE_PHOTO:
				$table = new Media_Item_Photo();
				break;
			case Media_Item::TYPE_VIDEO:
				$table = new Media_Item_Video();
				break;
			default:
				throw new Lib_Exception("Unknown media item type: '{$row->mediaType}'");
				break;
		}

		$item = $table->find($id)->current();

		return $item;
	}
}